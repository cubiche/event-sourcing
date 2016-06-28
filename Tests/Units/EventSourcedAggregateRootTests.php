<?php

/**
 * This file is part of the Cubiche package.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units;

use Cubiche\Core\Validator\Exception\ValidationException;
use Cubiche\Domain\EventSourcing\EventStore\EventStream;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\Event\PostTitleWasChanged;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\Event\PostWasCreated;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourced;
use Cubiche\Domain\EventSourcing\Tests\Fixtures\PostEventSourcedFactory;
use Cubiche\Domain\EventSourcing\Versioning\Version;

/**
 * EventSourcedAggregateRootTests class.
 *
 * Generated by TestGenerator on 2016-06-28 at 14:36:54.
 */
class EventSourcedAggregateRootTests extends TestCase
{
    /**
     * Test recordApplyAndPublishEvent method.
     */
    public function testRecordApplyAndPublishEvent()
    {
        $this
            ->given(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->array($post->recordedEvents())
                    ->hasSize(1)
        ;
    }

    /**
     * Test applyEvent method.
     */
    public function testApplyEventWithoutApplyMethod()
    {
        $this
            ->given(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->exception(function () use ($post) {
                    $post->publish();
                })->isInstanceOf(\BadMethodCallException::class)
        ;
    }

    /**
     * Test ClearEvents method.
     */
    public function testClearEvents()
    {
        $this
            ->given(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->then()
                ->array($post->recordedEvents())
                    ->hasSize(1)
            ->and()
            ->when($post->clearEvents())
            ->then()
                ->array($post->recordedEvents())
                    ->isEmpty()
        ;
    }

    /**
     * Test LoadFromHistory method.
     */
    public function testLoadFromHistory()
    {
        $this
            ->given($title = $this->faker->sentence())
            ->and($content = $this->faker->paragraph())
            ->and($newTitle = $this->faker->sentence())
            ->and(
                $post = PostEventSourcedFactory::create(
                    $title,
                    $content
                )
            )
            ->and($post->changeTitle($newTitle))
            ->and(
                $eventStream = new EventStream(
                    PostEventSourced::class,
                    $post->id(),
                    [
                        new PostWasCreated($post->id(), $title, $content),
                        new PostTitleWasChanged($post->id(), $newTitle),
                    ]
                )
            )
            ->when($other = PostEventSourced::loadFromHistory($eventStream))
            ->then()
                ->boolean($post->equals($other))
                    ->isTrue()
        ;
    }

    /**
     * Test version.
     */
    public function testVersion()
    {
        $this
            ->given(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->when($post->changeTitle($this->faker->sentence))
            ->then()
                ->integer($post->version()->modelVersion())
                    ->isEqualTo(0)
                ->integer($post->version()->aggregateVersion())
                    ->isEqualTo(2)
        ;

        $this
            ->given(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->and($version = new Version(10, 125))
            ->when($post->setVersion($version))
            ->then()
                ->integer($post->version()->modelVersion())
                    ->isEqualTo(10)
                ->integer($post->version()->aggregateVersion())
                    ->isEqualTo(125)
        ;
    }

    /**
     * Test validation.
     */
    public function testValidation()
    {
        $this
            ->given(
                $post = PostEventSourcedFactory::create(
                    $this->faker->sentence,
                    $this->faker->paragraph
                )
            )
            ->when($post->changeTitle($this->faker->sentence()))
            ->then()
                ->array($post->recordedEvents())
                    ->hasSize(2)
            ->and()
            ->exception(function () use ($post) {
                $post->changeTitle('');
            })->isInstanceOf(ValidationException::class)
            ->exception(function () use ($post) {
                $post->changeTitle(10);
            })->isInstanceOf(ValidationException::class)
        ;

        $this
            ->exception(function () {
                PostEventSourcedFactory::create('', $this->faker->paragraph);
            })->isInstanceOf(ValidationException::class)
        ;
    }
}
