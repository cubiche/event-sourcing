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

use Cubiche\Domain\EventPublisher\DomainEventPublisher;
use Cubiche\Domain\EventSourcing\AggregateRepository;
use Cubiche\Domain\EventSourcing\EventStore\InMemoryEventStore;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\Listener\PostPersistSubscriber;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\Listener\PostRemoveSubscriber;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\Listener\PrePersistSubscriber;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\Listener\PreRemoveSubscriber;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourced;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourcedFactory;
use Cubiche\Domain\Model\Tests\Fixtures\Post;
use Cubiche\Domain\Model\Tests\Fixtures\PostId;

/**
 * AggregateRepositoryTests class.
 *
 * Generated by TestGenerator on 2016-06-28 at 14:36:54.
 */
class AggregateRepositoryTests extends TestCase
{
    /**
     * @return AggregateRepository
     */
    protected function createRepository()
    {
        return new AggregateRepository(new InMemoryEventStore(), PostEventSourced::class);
    }

    /**
     * Test Persist method.
     */
    public function testPersist()
    {
        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($post->changeTitle($this->faker->sentence))
            ->when($repository->persist($post))
            ->then()
                ->object($repository->get($post->id()))
                    ->isEqualTo($post)
        ;

        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($post->clearEvents())
            ->when($repository->persist($post))
            ->then()
                ->variable($repository->get($post->id()))
                    ->isNull()
        ;

        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post = new Post(
                    PostId::fromNative(md5(rand())),
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->exception(function () use ($repository, $post) {
                    $repository->persist($post);
                })
                ->isInstanceOf(\InvalidArgumentException::class)
        ;

        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($post->changeTitle($this->faker->sentence))
            ->and($prePersistSubscriber = new PrePersistSubscriber(42))
            ->and($postPersistSubscriber = new PostPersistSubscriber())
            ->and(DomainEventPublisher::subscribe($prePersistSubscriber))
            ->and(DomainEventPublisher::subscribe($postPersistSubscriber))
            ->when($repository->persist($post))
            ->then()
                ->integer($post->version())
                    ->isEqualTo(84)
        ;
    }

    /**
     * Test PersistAll method.
     */
    public function testPersistAll()
    {
        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post1 = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and(
                $post2 = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($post1->changeTitle($this->faker->sentence))
            ->and($post2->changeTitle($this->faker->sentence))
            ->when($repository->persistAll(array($post1, $post2)))
            ->then()
                ->object($repository->get($post1->id()))
                    ->isEqualTo($post1)
                ->and()
                ->object($repository->get($post2->id()))
                    ->isEqualTo($post2)
        ;
    }

    /**
     * Test Remove method.
     */
    public function testRemove()
    {
        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($post->changeTitle($this->faker->sentence))
            ->when($repository->persist($post))
            ->then()
                ->object($repository->get($post->id()))
                    ->isEqualTo($post)
                ->and()
                ->when($repository->remove($post))
                ->then()
                    ->variable($repository->get($post->id()))
                        ->isNull()
        ;

        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post = new Post(
                    PostId::fromNative(md5(rand())),
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->exception(function () use ($repository, $post) {
                    $repository->remove($post);
                })
                ->isInstanceOf(\InvalidArgumentException::class)
        ;

        $this
            ->given($repository = $this->createRepository())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($repository->persist($post))
            ->and($preRemoveSubscriber = new PreRemoveSubscriber(42))
            ->and($postRemoveSubscriber = new PostRemoveSubscriber())
            ->and(DomainEventPublisher::subscribe($preRemoveSubscriber))
            ->and(DomainEventPublisher::subscribe($postRemoveSubscriber))
            ->when($repository->remove($post))
            ->then()
                ->integer($post->version())
                    ->isEqualTo(21)
        ;
    }
}