<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

class FilteringEventStore implements EventStoreInterface
{
    /**
     * @var EventStoreInterface
     */
    private $eventStore;

    /**
     * @var EventFilterInterface
     */
    private $filter;

    /**
     * @param EventStoreInterface $eventStore
     * @param EventFilterInterface $filter
     */
    public function __construct(EventStoreInterface $eventStore, EventFilterInterface $filter)
    {
        $this->eventStore = $eventStore;
        $this->filter     = $filter;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        if ($this->filter->isValid($event)) {
            $this->eventStore->store($event);
        }
    }

    /**
     * @param int|null $offset
     * @param int $limit
     * @return array
     */
    public function read($offset = null, $limit = 10)
    {
        return $this->eventStore->read($offset, $limit);
    }
}
