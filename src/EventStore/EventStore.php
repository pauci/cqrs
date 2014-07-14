<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;

interface EventStore
{
    public function store(DomainEventMessageInterface $event);
}
