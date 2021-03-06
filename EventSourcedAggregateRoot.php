<?php

/**
 * This file is part of the Cubiche package.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cubiche\Domain\EventSourcing;

use Cubiche\Core\Validator\Validator;
use Cubiche\Domain\EventPublisher\DomainEventPublisher;
use Cubiche\Domain\EventSourcing\Versioning\Version;
use Cubiche\Domain\EventSourcing\Versioning\VersionManager;
use Cubiche\Domain\EventSourcing\EventStore\EventStream;

/**
 * EventSourcedAggregateRoot trait.
 *
 * @author Ivannis Suárez Jerez <ivannis.suarez@gmail.com>
 */
trait EventSourcedAggregateRoot
{
    /**
     * @var int
     */
    protected $version;

    /**
     * @var DomainEventInterface[]
     */
    protected $recordedEvents = [];

    /**
     * @param DomainEventInterface $event
     */
    protected function recordApplyAndPublishEvent(DomainEventInterface $event)
    {
        Validator::assert($event);

        $this->incrementVersion();
        $event->setVersion($this->version());

        $this->recordEvent($event);
        $this->applyEvent($event);
        $this->publishEvent($event);
    }

    /**
     * @param DomainEventInterface $event
     */
    protected function applyEvent(DomainEventInterface $event)
    {
        $classParts = explode('\\', get_class($event));
        $method = 'apply'.end($classParts);

        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(
                "There is no method named '$method' that can be applied to '".get_class($this)."'. "
            );
        }

        $this->$method($event);
        $this->setVersion($event->version());
    }

    /**
     * @param DomainEventInterface $event
     */
    protected function publishEvent(DomainEventInterface $event)
    {
        DomainEventPublisher::publish($event);
    }

    /**
     * @param DomainEventInterface $event
     */
    protected function recordEvent(DomainEventInterface $event)
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function recordedEvents()
    {
        return $this->recordedEvents;
    }

    /**
     * Clear recorded events.
     */
    public function clearEvents()
    {
        $this->recordedEvents = [];
    }

    /**
     * @param EventStream $history
     *
     * @return AggregateRootInterface
     */
    public static function loadFromHistory(EventStream $history)
    {
        $reflector = new \ReflectionClass(static::class);

        /** @var EventSourcedAggregateRootInterface $aggregateRoot */
        $aggregateRoot = $reflector->newInstanceWithoutConstructor();
        $aggregateRoot->id = $history->aggregateId();
        $aggregateRoot->replay($history);

        return $aggregateRoot;
    }

    /**
     * @param EventStream $history
     */
    public function replay(EventStream $history)
    {
        foreach ($history->events() as $event) {
            $this->applyEvent($event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function version()
    {
        if ($this->version === null) {
            $this->version = VersionManager::versionOf($this);
        }

        return $this->version;
    }

    /**
     * Increment the current version.
     */
    protected function incrementVersion()
    {
        $version = $this->version();
        ++$version;

        $this->setVersion($version);
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
