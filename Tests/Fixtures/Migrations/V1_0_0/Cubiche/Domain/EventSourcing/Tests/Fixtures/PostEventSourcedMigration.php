<?php
/**
 * This file is part of the Cubiche package.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace V1_0_0\Cubiche\Domain\EventSourcing\Tests\Fixtures;

use Cubiche\Domain\EventSourcing\EventStore\EventStream;
use Cubiche\Domain\EventSourcing\Migrations\MigrationInterface;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourced;

/**
 * PostEventSourcedMigration class.
 *
 * Generated by MigrationGenerator on 2016-08-31 at 15:47:44.
 */
class PostEventSourcedMigration implements MigrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function migrate(EventStream $eventStream)
    {
        // TODO: Implement migrate() method.
        return $eventStream;
    }

    /**
     * {@inheritdoc}
     */
    public function aggregateClassName()
    {
        return PostEventSourced::class;
    }
}
