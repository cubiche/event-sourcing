<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units\Snapshot;

use Cubiche\Domain\EventSourcing\EventStore\InMemoryEventStore;
use Cubiche\Domain\EventSourcing\Snapshot\InMemorySnapshotStore;
use Cubiche\Domain\EventSourcing\Snapshot\Policy\EventsBasedSnapshottingPolicy;
use Cubiche\Domain\EventSourcing\Snapshot\Policy\NoSnapshottingPolicy;
use Cubiche\Domain\EventSourcing\Snapshot\Policy\SnapshottingPolicyInterface;
use Cubiche\Domain\EventSourcing\Snapshot\Snapshot;
use Cubiche\Domain\EventSourcing\Snapshot\SnapshotAggregateRepository;
use Cubiche\Domain\EventSourcing\Snapshot\SnapshotStoreInterface;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourced;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourcedFactory;
use Cubiche\Domain\EventSourcing\Tests\Units\EventSourcedAggregateRepositoryTests;
use Cubiche\Domain\EventSourcing\Utils\NameResolver;
use Cubiche\Domain\EventSourcing\Versioning\Version;

/**
 * SnapshotAggregateRepositoryTests class.
 *
 * Generated by TestGenerator on 2016-06-28 at 14:36:54.
 */
class SnapshotAggregateRepositoryTests extends EventSourcedAggregateRepositoryTests
{
    /**
     * @param SnapshotStoreInterface      $snapshotStore
     * @param SnapshottingPolicyInterface $snapshottingPolicy
     *
     * @return SnapshotAggregateRepository
     */
    protected function createRepository(
        SnapshotStoreInterface $snapshotStore = null,
        SnapshottingPolicyInterface $snapshottingPolicy = null
    ) {
        return new SnapshotAggregateRepository(
            new InMemoryEventStore(),
            $snapshotStore ? $snapshotStore : new InMemorySnapshotStore(),
            $snapshottingPolicy ? $snapshottingPolicy : new NoSnapshottingPolicy(),
            PostEventSourced::class
        );
    }

    /**
     * Test Get method.
     */
    public function testGet()
    {
        $this
            ->given($store = new InMemorySnapshotStore())
            ->and($repository = $this->createRepository($store))
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($version = new Version(0, 0, 231))
            ->and($post->setVersion($version))
            ->and($post->changeTitle($this->faker->sentence))
            ->and($snapshot = new Snapshot(NameResolver::resolve(get_class($post)), $post, new \DateTime()))
            ->when($repository->persist($post))
            ->and($store->persist($snapshot))
            ->then()
                ->object($repository->get($post->id()))
                    ->isEqualTo($post)
        ;
    }

    /**
     * Test Persist method.
     */
    public function testPersist()
    {
        parent::testPersist();

        $this
            ->given(
                $repository = $this->createRepository(
                    new InMemorySnapshotStore(),
                    new EventsBasedSnapshottingPolicy()
                )
            )
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($version = new Version(0, 0, 231))
            ->and($post->setVersion($version))
            ->and($post->changeTitle($this->faker->sentence))
            ->when($repository->persist($post))
            ->then()
                ->object($repository->get($post->id()))
                    ->isEqualTo($post)
        ;
    }
}
