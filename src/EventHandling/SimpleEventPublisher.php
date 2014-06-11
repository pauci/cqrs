<?php

namespace CQRS\EventHandling;

use CQRS\Domain\AggregateRoot;

class SimpleEventPublisher implements EventPublisher
{
    /** @var EventBus */
    private $eventBus;

    /** @var AggregateRoot[] */
    private $aggregateRoots = [];

    /**
     * @param EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    public function registerAggregate(AggregateRoot $aggregateRoot)
    {
        $this->aggregateRoots[] = $aggregateRoot;
    }

    public function publishEvents()
    {
        foreach ($this->aggregateRoots as $aggregateRoot) {
            $events = $aggregateRoot->pullDomainEvents();

            foreach ($events as $event) {
                $this->eventBus->publish($event);
            }
        }
    }
}
