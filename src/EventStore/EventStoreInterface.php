<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

interface EventStoreInterface
{
    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event);

    /**
     * @param int|null $offset
     * @param int $limit
     * @return array
     */
    public function read($offset = null, $limit = 10);
}
