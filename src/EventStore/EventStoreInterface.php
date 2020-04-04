<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use Ramsey\Uuid\UuidInterface;
use Traversable;

interface EventStoreInterface
{
    public function store(EventMessageInterface $event): void;

    /**
     * @return EventMessageInterface[]
     */
    public function read(int $offset = 0, int $limit = 10): array;

    /**
     * @return Traversable<EventMessageInterface>
     */
    public function iterate(UuidInterface $previousEventId = null): Traversable;
}
