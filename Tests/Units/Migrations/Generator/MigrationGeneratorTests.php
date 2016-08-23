<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units\Migrations\Generator;

use Cubiche\Domain\EventSourcing\Migrations\Generator\MigrationGenerator;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourced;
use Cubiche\Domain\EventSourcing\Tests\Units\TestCase;
use Cubiche\Domain\EventSourcing\Versioning\VersionIncrementType;
use Cubiche\Domain\EventSourcing\Versioning\VersionManager;

/**
 * MigrationGeneratorTests class.
 *
 * Generated by TestGenerator on 2016-08-10 at 10:18:25.
 */
class MigrationGeneratorTests extends TestCase
{
    /**
     * @return MigrationGenerator
     */
    protected function createGenerator()
    {
        return new MigrationGenerator($this->migrationsDirectory);
    }

    /**
     * Test Generate method.
     */
    public function testGenerate()
    {
        require_once __DIR__.'/../../../Fixtures/BlogEventSourced.php';

        $this
            ->given($generator = $this->createGenerator())
            ->and($aggregateClass = PostEventSourced::class)
            ->and($version = VersionManager::versionOfClass($aggregateClass))
            ->and($incrementType = VersionIncrementType::MINOR())
            ->when($generator->generate($aggregateClass, $version, $incrementType))
                ->then()
                    ->boolean(file_exists($this->getMigratorFileName($aggregateClass, $version)))
                        ->isTrue()
                    ->and()
                    ->exception(function () use ($generator, $aggregateClass, $version, $incrementType) {
                        $generator->generate($aggregateClass, $version, $incrementType);
                    })->isInstanceOf(\RuntimeException::class)
        ;

        $this
            ->given($generator = $this->createGenerator())
            ->and($aggregateClass = \BlogEventSourced::class)
            ->and($version = VersionManager::versionOfClass($aggregateClass))
            ->and($incrementType = VersionIncrementType::MAJOR())
            ->when($generator->generate($aggregateClass, $version, $incrementType))
                ->then()
                    ->boolean(file_exists($this->getMigratorFileName($aggregateClass, $version)))
                        ->isTrue()
                    ->boolean($generator->existsDirectory($version))
                        ->isTrue()
        ;
    }
}
