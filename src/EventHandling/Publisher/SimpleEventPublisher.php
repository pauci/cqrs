<?php

declare(strict_types=1);

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\Metadata;
use CQRS\EventHandling\EventBusInterface;
use CQRS\EventStore\EventStoreInterface;

class SimpleEventPublisher implements EventPublisherInterface
{
    private EventBusInterface $eventBus;

    private EventQueueInterface $queue;

    private EventStoreInterface $eventStore;

    private Metadata $additionalMetadata;

    /**
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

    public function getEventBus(): EventBusInterface
    {
        return $this->eventBus;
    }

    /**
     * @param Metadata|array $additionalMetadata
     */
    public function setAdditionalMetadata($additionalMetadata): void
    {
        $this->additionalMetadata = Metadata::from($additionalMetadata);
    }

    public function getAdditionalMetadata(): Metadata
    {
        return $this->additionalMetadata;
    }

    public function publishEvents(): void
    {
        $this->dispatchEvents($this->dequeueEvents());
    }

    /**
     * @return EventMessageInterface[]
     */
    protected function dequeueEvents(): array
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
    protected function dispatchEvents(array $events): void
    {
        foreach ($events as $event) {
            if ($this->eventStore) {
                $this->eventStore->store($event);
            }

            $this->eventBus->publish($event);
        }
    }
}
