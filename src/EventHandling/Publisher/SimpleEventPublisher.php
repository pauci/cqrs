<?php

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;
use CQRS\EventHandling\EventBusInterface;

class SimpleEventPublisher implements EventPublisherInterface
{
    /** @var EventBusInterface */
    private $eventBus;

    /** @var AggregateRootInterface[] */
    private $aggregateRoots = [];

    /**
     * @param EventBusInterface $eventBus
     */
    public function __construct(EventBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     */
    public function registerAggregate(AggregateRootInterface $aggregateRoot)
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
