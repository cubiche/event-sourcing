<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cubiche\Domain\EventSourcing\Tests\Units;

use Cubiche\Domain\EventSourcing\DomainEventId;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\Event\PostWasCreated;
use Cubiche\Domain\Model\Tests\Fixtures\PostId;

/**
 * DomainEventTests class.
 *
 * Generated by TestGenerator on 2016-06-28 at 14:36:54.
 */
class DomainEventTests extends TestCase
{
    /**
     * Test EventId method.
     */
    public function testEventId()
    {
        $this
            ->given($postId = PostId::fromNative(md5(rand())))
            ->when(
                $event = new PostWasCreated(
                    $postId,
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->object($event->eventId())
                    ->isInstanceOf(DomainEventId::class)
        ;
    }

    /**
     * Test AggregateId method.
     */
    public function testAggregateId()
    {
        $this
            ->given($postId = PostId::fromNative(md5(rand())))
            ->when(
                $event = new PostWasCreated(
                    $postId,
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->object($event->aggregateId())
                    ->isEqualTo($postId)
        ;
    }

    /**
     * Test Version method.
     */
    public function testVersion()
    {
        $this
            ->given($postId = PostId::fromNative(md5(rand())))
            ->when(
                $event = new PostWasCreated(
                    $postId,
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->integer($event->version())
                    ->isEqualTo(0)
                ->and()
                ->when($event->setVersion(165))
                ->then()
                    ->integer($event->version())
                        ->isEqualTo(165)
        ;
    }

    /**
     * Test ToArray method.
     */
    public function testToArray()
    {
        $this
            ->given(
                $postId = PostId::fromNative(md5(rand())),
                $title = $this->faker->sentence,
                $content = $this->faker->sentence
            )
            ->and()
            ->when($event = new PostWasCreated($postId, $title, $content))
            ->and($eventId = $event->eventId())
            ->then()
            ->array($event->toArray())
                ->child['metadata'](function ($metadata) use ($postId, $eventId) {
                    $metadata
                        ->hasKey('occurredOn')
                        ->object['aggregateId']
                            ->isEqualTo($postId)
                        ->object['eventId']
                            ->isEqualTo($eventId)
                        ->string['eventType']
                            ->isEqualTo(PostWasCreated::class)
                    ;
                })
                ->child['payload'](function ($payload) use ($title, $content) {
                    $payload
                        ->isEqualTo(array(
                            'title' => $title,
                            'content' => $content,
                        ))
                    ;
                })
        ;
    }

    /**
     * Test FromArray method.
     */
    public function testFromArray()
    {
        $this
            ->given(
                $postId = PostId::fromNative(md5(rand())),
                $title = $this->faker->sentence,
                $content = $this->faker->sentence
            )
            ->and()
            ->when($event = new PostWasCreated($postId, $title, $content))
            ->and($eventId = $event->eventId())
            ->and($eventFromArray = PostWasCreated::fromArray($event->toArray()))
            ->then()
                ->object($eventFromArray)
                    ->isInstanceOf(PostWasCreated::class)
                ->object($event->eventId())
                    ->isEqualTo($eventFromArray->eventId())
                ->object($event->occurredOn())
                    ->isEqualTo($eventFromArray->occurredOn())
                ->string($event->eventName())
                    ->isEqualTo($eventFromArray->eventName())
                ->string($event->title())
                    ->isEqualTo($eventFromArray->title())
                ->string($event->content())
                    ->isEqualTo($eventFromArray->content())
                ->boolean($event->isPropagationStopped())
                    ->isEqualTo($eventFromArray->isPropagationStopped())
        ;
    }
}
