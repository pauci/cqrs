<?php

namespace CQRS\Domain\Model;

use Countable;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\EventHandling\EventInterface;

/**
 * Container for events related to a single aggregate. The container will wrap registered event (payload) and metadata
 * in an GenericDomainEventMessage and automatically assign the aggregate identifier and the next sequence number.
 */
class EventContainer implements Countable
{
    /** @var GenericDomainEventMessage[] */
    private $events = [];

    /** @var string */
    private $aggregateType;

    /** @var mixed */
    private $aggregateId;

    /** @var int */
    private $lastSequenceNumber;

    /** @var int */
    private $lastCommittedSequenceNumber;

    /**
     * @param string $aggregateType
     * @param mixed $aggregateId
     */
    public function __construct($aggregateType, &$aggregateId)
    {
        $this->aggregateType = $aggregateType;
        $this->aggregateId   = &$aggregateId;
    }

    /**
     * Add an event to this container.
     *
     * @param EventInterface $payload
     * @param Metadata|array $metadata
     * @return GenericDomainEventMessage
     */
    public function addEvent(EventInterface $payload, $metadata = null)
    {
        $event = new GenericDomainEventMessage(
            $this->aggregateType,
            $this->aggregateId,
            $this->newSequenceNumber(),
            $payload,
            $metadata
        );

        $this->lastSequenceNumber = $event->getSequenceNumber();
        $this->events[] = $event;
        return $event;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * @return GenericDomainEventMessage[]
     */
    public function pullEvents()
    {
        $this->lastCommittedSequenceNumber = $this->getLastSequenceNumber();
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * @return int
     */
    private function getLastSequenceNumber()
    {
        if (empty($this->events)) {
            return $this->lastCommittedSequenceNumber;
        }
        if ($this->lastSequenceNumber === null) {
            $event = end($this->events);
            $this->lastSequenceNumber = $event->getSequenceNumber();
        }
        return $this->lastSequenceNumber;
    }

    /**
     * @return int
     */
    private function newSequenceNumber()
    {
        $currentSequenceNumber = $this->getLastSequenceNumber();
        if ($currentSequenceNumber === null) {
            return 0;
        }
        return $currentSequenceNumber + 1;
    }
}
