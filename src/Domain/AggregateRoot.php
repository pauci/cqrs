<?php

namespace CQRS\Domain;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private $events = [];

    /**
     * @param DomainEvent $event
     */
    public function raiseDomainEvent(DomainEvent $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function pullDomainEvents()
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * @return Id
     */
    abstract public function getId();
}
