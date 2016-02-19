<?php

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Message\DomainEventMessageInterface;

class DomainEventQueue implements EventQueueInterface
{
    /**
     * @var IdentityMapInterface
     */
    private $identityMap;

    /**
     * @param IdentityMapInterface $identityMap
     */
    public function __construct(IdentityMapInterface $identityMap)
    {
        $this->identityMap = $identityMap;
    }

    /**
     * @return DomainEventMessageInterface[]
     */
    public function dequeueAllEvents()
    {
        $dequeueEvents = [];

        foreach ($this->identityMap->getAll() as $aggregateRoot) {
            foreach ($aggregateRoot->getUncommittedEvents() as $event) {
                $dequeueEvents[] = $event;
            }
            $aggregateRoot->commitEvents();
        }

        return $dequeueEvents;
    }
}
