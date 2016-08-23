<?php

/**
 * This file is part of the Cubiche package.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
set_time_limit(0);
$loaded = false;
foreach (array(__DIR__.'/../../autoload.php', __DIR__.'/../../../../../vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        require $file;
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    die(
        'You need to set up the project dependencies using the following commands:'.PHP_EOL.
        'wget http://getcomposer.org/composer.phar'.PHP_EOL.
        'php composer.phar install'.PHP_EOL
    );
}

use Cubiche\Core\Bus\Command\CommandBus;
use Cubiche\Core\Console\ConsoleApplication;
use Cubiche\Core\Metadata\ClassMetadataFactory;
use Cubiche\Domain\EventPublisher\DomainEventPublisher;
use Cubiche\Domain\EventSourcing\Metadata\Driver\AnnotationDriver;
use Cubiche\Domain\EventSourcing\Migrations\Cli\ApplicationConfig;
use Cubiche\Domain\EventSourcing\Migrations\Cli\Command\MigrationsGenerateCommand;
use Cubiche\Domain\EventSourcing\Migrations\Cli\Command\MigrationsMigrateCommand;
use Cubiche\Domain\EventSourcing\Migrations\Cli\Command\MigrationsStatusCommand;
use Cubiche\Domain\EventSourcing\Migrations\Cli\MigrationsService;
use Cubiche\Domain\EventSourcing\Migrations\Migrator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\FilesystemCache;

/*
 * @var string
 */
$migrationsDirectory = __DIR__.'/Migrations';

/*
 * @var string
 */
$cacheDirectory = __DIR__.'/Cache';

/**
 * @param callable|ApplicationConfig $config
 * @param string                     $migrationsDirectory
 * @param string                     $cacheDirectory
 *
 * @return ConsoleApplication
 */
function createApplication($config, $migrationsDirectory, $cacheDirectory)
{
    $commandBus = CommandBus::create();
    $eventBus = DomainEventPublisher::eventBus();

    $migrationsService = new MigrationsService(
        new Migrator(getClassMetadataFactory($cacheDirectory), $migrationsDirectory)
    );

    $commandBus->addHandler(MigrationsGenerateCommand::class, $migrationsService);
    $commandBus->addHandler(MigrationsMigrateCommand::class, $migrationsService);
    $commandBus->addHandler(MigrationsStatusCommand::class, $migrationsService);

    return new ConsoleApplication($config, $commandBus, $eventBus);
}

/**
 * @param string $cacheDirectory
 *
 * @return ClassMetadataFactory
 */
function getClassMetadataFactory($cacheDirectory)
{
    $reader = new CachedReader(
        new AnnotationReader(),
        new FilesystemCache($cacheDirectory),
        $debug = true
    );

    AnnotationRegistry::registerFile(
        __DIR__.'/../Metadata/Annotations/Migratable.php'
    );

    $driver = new AnnotationDriver($reader, [__DIR__.'/../Tests/Fixtures']);
    $driver->addExcludePaths([
        __DIR__.'/../Tests/Fixtures/Event',
        __DIR__.'/../Tests/Fixtures/Listener',
    ]);

    return new ClassMetadataFactory($driver);
}

$cli = createApplication(new ApplicationConfig(), $migrationsDirectory, $cacheDirectory);
//$cli->run();
