<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

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
}
