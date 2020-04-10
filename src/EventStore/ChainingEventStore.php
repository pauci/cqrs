<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Exception;
use Ramsey\Uuid\UuidInterface;
use Traversable;

class ChainingEventStore implements EventStoreInterface
{
    /**
     * @var EventStoreInterface[]
     */
    private array $eventStores;

    /**
     * @param EventStoreInterface[] $eventStores
     */
    public function __construct(array $eventStores)
    {
        $this->eventStores = $eventStores;
    }

    public function store(EventMessageInterface $event): void
    {
        foreach ($this->eventStores as $eventStore) {
            $eventStore->store($event);
        }
    }

    /**
     * @return EventMessageInterface[]
     */
    public function read(int $offset = 0, int $limit = 10): array
    {
        throw new Exception\BadMethodCallException(sprintf('%s does not support reading', self::class));
    }

    /**
     * @return Traversable<EventMessageInterface>
     */
    public function iterate(UuidInterface $previousEventId = null): Traversable
    {
        throw new Exception\BadMethodCallException(sprintf('%s does not support iterating', self::class));
    }
}
