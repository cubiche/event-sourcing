<?php

/**
 * This file is part of the Cubiche package.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cubiche\Domain\EventSourcing\Tests\Units\Snapshot;

use Cubiche\Domain\EventSourcing\Snapshot\Snapshot;
use Cubiche\Domain\EventSourcing\Snapshot\SnapshotStoreInterface;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourcedFactory;
use Cubiche\Domain\EventSourcing\Tests\Units\TestCase;

/**
 * SnapshotStoreTestCase class.
 *
 * Generated by TestGenerator on 2016-06-28 at 14:36:54.
 */
abstract class SnapshotStoreTestCase extends TestCase
{
    /**
     * @return SnapshotStoreInterface
     */
    abstract protected function createStore();

    /**
     * Test Persist method.
     */
    public function testPersist()
    {
        $this
            ->given($store = $this->createStore())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($snapshotName = 'Posts-'.$post->id()->toNative())
            ->and($snapshot = new Snapshot($snapshotName, $post))
            ->when($store->persist($snapshot))
            ->then()
                ->object($store->load($snapshotName))
                    ->isEqualTo($snapshot)
        ;
    }

    /**
     * Test Load method.
     */
    public function testLoad()
    {
        $this
            ->given($store = $this->createStore())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($snapshotName = 'Posts-'.$post->id()->toNative())
            ->and($snapshotNameFake = 'Blogs-'.$post->id()->toNative())
            ->and($snapshot = new Snapshot($snapshotName, $post))
            ->when($store->persist($snapshot))
            ->then()
                ->variable($store->load($snapshotNameFake))
                    ->isNull()
                ->and()
                ->when($result = $store->load($snapshotName))
                ->then()
                    ->object($result)
                        ->isEqualTo($snapshot);
    }

    /**
     * Test Remove method.
     */
    public function testRemove()
    {
        $this
            ->given($store = $this->createStore())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($snapshotName = 'Posts-'.$post->id()->toNative())
            ->and($snapshotNameFake = 'Blogs-'.$post->id()->toNative())
            ->and($snapshot = new Snapshot($snapshotName, $post))
            ->and($store->persist($snapshot))
            ->when($store->remove($snapshotNameFake))
            ->then()
                ->object($store->load($snapshotName))
                    ->isEqualTo($snapshot)
                ->and()
                ->when($store->remove($snapshotName))
                ->then()
                    ->variable($store->load($snapshotName))
                        ->isNull()
        ;
    }
}
