<?php

namespace CQRS\EventHandling\Publisher;

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
        $this->eventBus           = $eventBus;
        $this->queue              = $queue;
        $this->eventStore         = $eventStore;
        $this->additionalMetadata = Metadata::from($additionalMetadata);
    }

    /**
     * @return EventBusInterface
     */
    public function getEventBus()
    {
        return $this->eventBus;
    }

    public function publishEvents()
    {
        if (!$this->queue) {
            return;
        }

        foreach ($this->queue->dequeueAllEvents() as $event) {
            if (!empty($this->additionalMetadata)) {
                $event = $event->addMetadata($this->additionalMetadata);
            }

            if ($this->eventStore) {
                $this->eventStore->store($event);
            }

            $this->eventBus->publish($event);
        }
    }
}
