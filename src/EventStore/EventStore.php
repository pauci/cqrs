<?php

namespace CQRS\EventStore;

use CQRS\EventHandling\DomainEvent;

interface EventStore
{
    public function store(DomainEvent $event);
}
