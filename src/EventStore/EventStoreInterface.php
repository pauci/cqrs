<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventInterface;

interface EventStoreInterface
{
    public function store(DomainEventInterface $event);
}
