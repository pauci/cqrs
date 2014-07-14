<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\DomainEventMessageInterface;
use Rhumsaa\Uuid\Uuid;

abstract class AbstractAggregateRoot implements AggregateRootInterface
{
    /** @var DomainEventMessageInterface[] */
    private $events = [];

    /** @var bool */
    private $deleted = false;

    /**
     * @return Uuid|int
     */
    abstract public function getId();

    /**
     * @return DomainEventMessageInterface[]
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
     * @param DomainEventMessageInterface $event
     */
    protected function raiseDomainEvent(DomainEventMessageInterface $event)
    {
        $this->events[] = $event->setAggregate($this);
    }

    protected function markAsDeleted()
    {
        $this->deleted = true;
    }
}
