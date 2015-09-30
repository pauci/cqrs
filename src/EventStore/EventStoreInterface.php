<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use Ramsey\Uuid\Uuid;
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
     * @param Uuid|null $previousEventId
     * @return Traversable
     */
    public function iterate(Uuid $previousEventId = null);
}
