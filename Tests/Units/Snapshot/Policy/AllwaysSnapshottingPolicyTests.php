<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units\Snapshot\Policy;

use Cubiche\Domain\EventSourcing\Snapshot\Policy\AllwaysSnapshottingPolicy;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourcedFactory;
use Cubiche\Domain\EventSourcing\Tests\Units\TestCase;

/**
 * AllwaysSnapshottingPolicyTests class.
 *
 * Generated by TestGenerator on 2016-07-26 at 14:15:46.
 */
class AllwaysSnapshottingPolicyTests extends TestCase
{
    /**
     * Test ShouldCreateSnapshot method.
     */
    public function testShouldCreateSnapshot()
    {
        $this
            ->given($policy = new AllwaysSnapshottingPolicy())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->boolean($policy->shouldCreateSnapshot($post))
                    ->isTrue()
        ;
    }
}
