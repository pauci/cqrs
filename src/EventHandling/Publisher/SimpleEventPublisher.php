<?php

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\Metadata;
use CQRS\EventHandling\EventBusInterface;
use CQRS\EventStore\EventStoreInterface;

class SimpleEventPublisher implements EventPublisherInterface
{
    /**
     * @var EventBusInterface
     */
    private $eventBus;

    /**
     * @var EventQueueInterface
     */
    private $queue;

    /**
     * @var EventStoreInterface
     */
    private $eventStore;

    /**
     * @var Metadata
     */
    private $additionalMetadata;

    /**
     * @param EventBusInterface $eventBus
     * @param EventQueueInterface $queue
     * @param EventStoreInterface $eventStore
     * @param Metadata|array $additionalMetadata
     */
    public function __construct(
        EventBusInterface $eventBus,
        EventQueueInterface $queue = null,
        EventStoreInterface $eventStore = null,
        $additionalMetadata = null
    ) {
        $this->eventBus   = $eventBus;
        $this->queue      = $queue;
        $this->eventStore = $eventStore;

        if ($additionalMetadata !== null) {
            $this->additionalMetadata = Metadata::from($additionalMetadata);
        }
    }

    /**
     * @return EventBusInterface
     */
    public function getEventBus()
    {
        return $this->eventBus;
    }

    /**
     * @param Metadata|array $additionalMetadata
     */
    public function setAdditionalMetadata($additionalMetadata)
    {
        $this->additionalMetadata = Metadata::from($additionalMetadata);
    }

    /**
     * @return Metadata
     */
    public function getAdditionalMetadata()
    {
        return $this->additionalMetadata;
    }

    public function publishEvents()
    {
        $this->dispatchEvents($this->dequeueEvents());
    }

    /**
     * @return EventMessageInterface[]
     */
    protected function dequeueEvents()
    {
        if (!$this->queue) {
            return [];
        }

        $events = $this->queue->dequeueAllEvents();
        if ($this->additionalMetadata) {
            foreach ($events as &$event) {
                $event = $event->addMetadata($this->additionalMetadata);
            }
        }
        return $events;
    }

    /**
     * @param EventMessageInterface[] $events
     */
    protected function dispatchEvents(array $events)
    {
        foreach ($events as $event) {
            if ($this->eventStore) {
                $this->eventStore->store($event);
            }

            $this->eventBus->publish($event);
        }
    }
}
