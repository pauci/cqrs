<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use Ramsey\Uuid\UuidInterface;
use Traversable;

interface EventStoreInterface
{
    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event);

    /**
     * @param int|null $offset
     * @param int $limit
     * @return EventMessageInterface[]
     */
    public function read($offset = null, $limit = 10);

    /**
     * @param null|UuidInterface $previousEventId
     * @return Traversable
     */
    public function iterate(UuidInterface $previousEventId = null);
}
