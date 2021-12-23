<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

interface EventStoreInterface
{
    public function store(EventMessageInterface $event): void;
}
