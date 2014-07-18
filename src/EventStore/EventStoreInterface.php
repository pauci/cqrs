<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventInterface;

interface EventStoreInterface
{
    /**
     * @param DomainEventInterface $event
     */
    public function store(DomainEventInterface $event);

    /**
     * @param int|null $offset
     * @param int $limit
     * @return array
     */
    public function read($offset = null, $limit = 10);
}
