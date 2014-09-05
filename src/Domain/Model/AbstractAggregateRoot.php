<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\EventHandling\EventInterface;

abstract class AbstractAggregateRoot implements AggregateRootInterface
{
    /** @var EventContainer */
    private $eventContainer;

    /** @var bool */
    private $deleted = false;

    /**
     * @return mixed
     */
    abstract public function getId();

    /**
     * @param EventInterface $event
     * @param array $metadata
     */
    protected function raiseDomainEvent(EventInterface $event, array $metadata = [])
    {
        $this->getEventContainer()->addEvent($event, $metadata);
    }

    /**
     * @return GenericDomainEventMessage[]
     */
    public function pullDomainEvents()
    {
        if ($this->eventContainer === null) {
            return [];
        }
        return $this->eventContainer->pullEvents();
    }

    /**
     * Marks this aggregate as deleted, instructing a Repository to remove that aggregate at an appropriate time
     */
    protected function markAsDeleted()
    {
        $this->deleted = true;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return EventContainer
     */
    private function getEventContainer()
    {
        if ($this->eventContainer === null) {
            $type = get_class($this);
            $id = &$this->getId();
            $this->eventContainer = new EventContainer($type, $id);
        }
        return $this->eventContainer;
    }
}
