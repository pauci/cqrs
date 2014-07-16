<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\AbstractDomainEvent;
use CQRS\Domain\Message\DomainEventInterface;
use Rhumsaa\Uuid\Uuid;

abstract class AbstractAggregateRoot implements AggregateRootInterface
{
    /** @var DomainEventInterface[] */
    private $events = [];

    /** @var bool */
    private $deleted = false;

    /**
     * @return Uuid|int
     */
    abstract public function getId();

    /**
     * @return DomainEventInterface[]
     */
    public function pullDomainEvents()
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param DomainEventInterface $event
     */
    protected function raiseDomainEvent(DomainEventInterface $event)
    {
        if ($event instanceof AbstractDomainEvent) {
            $event = $event->setAggregate($this);
        }

        $this->events[] = $event;
    }

    protected function markAsDeleted()
    {
        $this->deleted = true;
    }
}
