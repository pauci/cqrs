<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;

interface EventStoreInterface
{
    /**
     * @param DomainEventMessageInterface $event
     */
    public function store(DomainEventMessageInterface $event);

    /**
     * @param int|null $offset
     * @param int $limit
     * @return array
     */
    public function read($offset = null, $limit = 10);
}
