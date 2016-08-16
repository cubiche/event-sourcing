<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units\Migrations\Cli\Command;

use Cubiche\Domain\EventSourcing\Migrations\Cli\Command\MigrationsStatusCommand;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourced;
use Cubiche\Domain\EventSourcing\Tests\Units\TestCase;

/**
 * MigrationsStatusCommandTests class.
 *
 * Generated by TestGenerator on 2016-08-10 at 10:26:45.
 */
class MigrationsStatusCommandTests extends TestCase
{
    /**
     * Test Aggregate method.
     */
    public function testAggregate()
    {
        $this
            ->given($command = new MigrationsStatusCommand())
            ->then()
                ->variable($command->aggregate())
                    ->isNull()
        ;
    }

    /**
     * Test SetAggregate method.
     */
    public function testSetAggregate()
    {
        $this
            ->given($command = new MigrationsStatusCommand('foo'))
            ->when($command->setAggregate(PostEventSourced::class))
            ->then()
                ->string($command->aggregate())
                    ->isEqualTo(PostEventSourced::class)
        ;
    }
}
