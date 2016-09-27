<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units\Event;

use Cubiche\Domain\EventSourcing\Event\PostPersistEvent;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourcedFactory;
use Cubiche\Domain\EventSourcing\Tests\Units\TestCase;

/**
 * PostPersistEventTests class.
 *
 * Generated by TestGenerator on 2016-07-26 at 14:15:46.
 */
class PostPersistEventTests extends TestCase
{
    /**
     * Test Aggregate method.
     */
    public function testAggregate()
    {
        $this
            ->given(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($event = new PostPersistEvent($post))
            ->then()
                ->object($event->aggregate())
                    ->isEqualTo($post)
        ;
    }
}
