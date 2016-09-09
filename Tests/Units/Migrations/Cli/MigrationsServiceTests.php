<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units\Migrations\Cli;

use Cubiche\Domain\EventSourcing\EventStore\InMemoryEventStore;
use Cubiche\Domain\EventSourcing\Migrations\Cli\Command\MigrationsGenerateCommand;
use Cubiche\Domain\EventSourcing\Migrations\Cli\Command\MigrationsStatusCommand;
use Cubiche\Domain\EventSourcing\Migrations\Cli\MigrationsService;
use Cubiche\Domain\EventSourcing\Migrations\Migration;
use Cubiche\Domain\EventSourcing\Migrations\MigrationInterface;
use Cubiche\Domain\EventSourcing\Migrations\Migrator;
use Cubiche\Domain\EventSourcing\Migrations\Store\InMemoryMigrationStore;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourced;
use Cubiche\Domain\EventSourcing\Tests\Units\TestCase;
use Cubiche\Domain\EventSourcing\Versioning\Version;
use Cubiche\Tests\Generator\ClassUtils;
use Webmozart\Console\Api\IO\Input;
use Webmozart\Console\Api\IO\IO;
use Webmozart\Console\Api\IO\Output;
use Webmozart\Console\IO\InputStream\NullInputStream;
use Webmozart\Console\IO\OutputStream\BufferedOutputStream;

/**
 * MigrationsServiceTests class.
 *
 * Generated by TestGenerator on 2016-08-10 at 10:18:25.
 */
class MigrationsServiceTests extends TestCase
{
    /**
     * @var BufferedOutputStream
     */
    protected $output;

    /**
     * @return MigrationsService
     */
    protected function createService()
    {
        return new MigrationsService(
            new Migrator(
                $this->getClassMetadataFactory(),
                new InMemoryMigrationStore(),
                new InMemoryEventStore(),
                $this->migrationsDirectory
            )
        );
    }

    /**
     * @param string $filename
     *
     * @return MigrationInterface
     */
    protected function getMigratorClass($filename)
    {
        $classes = ClassUtils::getClassesInFile($filename);
        foreach ($classes as $className) {
            $reflector = new \ReflectionClass($className);

            return $reflector->newInstanceWithoutConstructor();
        }

        return;
    }

    /**
     * @return IO
     */
    protected function getIO()
    {
        $this->output = new BufferedOutputStream();

        return new IO(
            new Input(new NullInputStream()),
            new Output($this->output),
            new Output(new BufferedOutputStream())
        );
    }

    /**
     * Test MigrationsGenerate method.
     */
    public function testMigrationsGenerate()
    {
        require_once __DIR__.'/../../../Fixtures/BlogEventSourced.php';

        $this
            ->given($service = $this->createService())
            ->and($command = new MigrationsGenerateCommand(false))
            ->and($command->setIo($this->getIO()))
            ->and($version = Version::fromString('0.1.0'))
            ->and($migrationFilename1 = $this->getMigratorFileName(PostEventSourced::class, $version))
            ->and($migrationFilename2 = $this->getMigratorFileName(\BlogEventSourced::class, $version))
            ->when($service->migrationsGenerate($command))
            ->and($migrationClass1 = $this->getMigratorClass($migrationFilename1))
            ->and($migrationClass2 = $this->getMigratorClass($migrationFilename2))
            ->then()
                ->boolean(file_exists($migrationFilename1))
                    ->isTrue()
                ->boolean(file_exists($migrationFilename2))
                    ->isTrue()
                ->string($migrationClass1->aggregateClassName())
                    ->isEqualTo(PostEventSourced::class)
                ->string($migrationClass2->aggregateClassName())
                    ->isEqualTo(\BlogEventSourced::class)
                ->string($this->output->fetch())
                    ->contains('Generating migrations classes for version')
                    ->contains('successfully generated')
                ->and()
                ->when($service->migrationsGenerate($command))
                    ->string($this->output->fetch())
                        ->contains('A project migration with version '.$version->__toString().' already exists.')
        ;

        $this
            ->given($service = $this->createService())
            ->and($command = new MigrationsGenerateCommand(false))
            ->and($command->setIo($this->getIO()))
            ->and($version = Version::fromString('0.2.0'))
            ->and($migrationFilename1 = $this->getMigratorFileName(PostEventSourced::class, $version))
            ->and($migrationFilename2 = $this->getMigratorFileName(\BlogEventSourced::class, $version))
            ->when($service->migrationsGenerate($command))
            ->and($migrationClass1 = $this->getMigratorClass($migrationFilename1))
            ->and($migrationClass2 = $this->getMigratorClass($migrationFilename2))
            ->then()
                ->boolean(file_exists($migrationFilename1))
                    ->isTrue()
                ->boolean(file_exists($migrationFilename2))
                    ->isTrue()
                ->string($migrationClass1->aggregateClassName())
                    ->isEqualTo(PostEventSourced::class)
                ->string($migrationClass2->aggregateClassName())
                    ->isEqualTo(\BlogEventSourced::class)
                ->string($this->output->fetch())
                    ->contains('Generating migrations classes for version')
                    ->contains('successfully generated')
        ;

        $this
            ->given($service = $this->createService())
            ->and($command = new MigrationsGenerateCommand(true))
            ->and($command->setIo($this->getIO()))
            ->and($version = Version::fromString('1.0.0'))
            ->and($migrationFilename1 = $this->getMigratorFileName(PostEventSourced::class, $version))
            ->and($migrationFilename2 = $this->getMigratorFileName(\BlogEventSourced::class, $version))
            ->when($service->migrationsGenerate($command))
            ->and($migrationClass1 = $this->getMigratorClass($migrationFilename1))
            ->and($migrationClass2 = $this->getMigratorClass($migrationFilename2))
            ->then()
                ->boolean(file_exists($migrationFilename1))
                    ->isTrue()
                ->boolean(file_exists($migrationFilename2))
                    ->isTrue()
                ->string($migrationClass1->aggregateClassName())
                    ->isEqualTo(PostEventSourced::class)
                ->string($migrationClass2->aggregateClassName())
                    ->isEqualTo(\BlogEventSourced::class)
        ;
    }

    /**
     * Test MigrationsMigrate method.
     */
    public function testMigrationsMigrate()
    {
        // todo: Implement testMigrationsMigrate().
    }

    /**
     * Test MigrationsStatus method.
     */
    public function testMigrationsStatus()
    {
        $this
            ->given($service = $this->createService())
            ->and($command = new MigrationsStatusCommand())
            ->and($command->setIo($this->getIO()))
            ->when($service->migrationsStatus($command))
            ->then()
                ->string($this->output->fetch())
                    ->contains(' Current Version      <c2>0</c2>')
                    ->contains(' Latest Version       <c2>none</c2>')
                    ->contains(' Next Version         <c2>none</c2>')
                    ->contains(' Executed Migrations  <c2>0</c2>')
                    ->contains(' Available Migrations <c2>0</c2>')
                    ->contains(' New Migrations       <c2>0</c2>')
        ;

        $this->migrationsDirectory = __DIR__.'/../../../Fixtures/Event';

        $this
            ->given($service = $this->createService())
            ->and($command = new MigrationsStatusCommand())
            ->and($command->setIo($this->getIO()))
            ->when($service->migrationsStatus($command))
            ->then()
                ->string($this->output->fetch())
                    ->contains('Invalid migration directory')
        ;

        $this->migrationsDirectory = __DIR__.'/../../../Fixtures/Migrations';

        $this
            ->given($service = $this->createService())
            ->and($command = new MigrationsStatusCommand())
            ->and($command->setIo($this->getIO()))
            ->when($service->migrationsStatus($command))
            ->then()
                ->string($this->output->fetch())
                    ->contains(' Current Version      <c2>0</c2>')
                    ->contains(' Latest Version       <c2>1.1.0</c2>')
                    ->contains(' Next Version         <c2>0.1.0</c2>')
                    ->contains(' Executed Migrations  <c2>0</c2>')
                    ->contains(' Available Migrations <c2>4</c2>')
                    ->contains(' New Migrations       <c2>4</c2>')
        ;

        require_once __DIR__.'/../../../Fixtures/BlogEventSourced.php';

        $aggregates = [PostEventSourced::class, \BlogEventSourced::class];

        $migratorStore = new InMemoryMigrationStore();
        $migratorStore->persist(
            new Migration(
                $aggregates,
                Version::fromString('0.1.0'),
                \DateTime::createFromFormat('Y-m-d H:i:s', '2016-08-26 14:12:00')
            )
        );
        $migratorStore->persist(
            new Migration(
                $aggregates,
                Version::fromString('0.2.0'),
                \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-01 18:30:00')
            )
        );

        $migrator = new Migrator(
            $this->getClassMetadataFactory(),
            $migratorStore,
            new InMemoryEventStore(),
            $this->migrationsDirectory
        );

        $service = new MigrationsService($migrator);

        $this
            ->given($command = new MigrationsStatusCommand())
            ->and($command->setIo($this->getIO()))
            ->when($service->migrationsStatus($command))
            ->then()
                ->string($this->output->fetch())
                    ->contains(' Current Version      <c2>0.2.0 (2016-09-01 18:30:00)</c2>')
                    ->contains(' Latest Version       <c2>1.1.0</c2>')
                    ->contains(' Next Version         <c2>1.0.0</c2>')
                    ->contains(' Executed Migrations  <c2>2</c2>')
                    ->contains(' Available Migrations <c2>4</c2>')
                    ->contains(' New Migrations       <c2>2</c2>')
        ;
    }
}
