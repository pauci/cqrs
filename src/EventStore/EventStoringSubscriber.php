<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;

class EventStoringSubscriber
{
    /** @var EventStoreInterface */
    private $eventStore;

    /**
     * @param EventStoreInterface $eventStore
     */
    public function __construct(EventStoreInterface $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function onEventPublished(EventMessageInterface $event)
    {
        if (!$event instanceof DomainEventMessageInterface) {
            return;
        }

        $this->eventStore->store($event);
    }
} 
