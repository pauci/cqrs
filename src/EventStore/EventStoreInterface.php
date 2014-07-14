<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;

interface EventStoreInterface
{
    public function store(DomainEventMessageInterface $event);
}
