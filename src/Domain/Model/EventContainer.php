<?php

namespace CQRS\Domain\Model;

use Countable;
use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Exception\InvalidArgumentException;
use CQRS\Exception\RuntimeException;

/**
 * Container for events related to a single aggregate. The container will wrap registered event (payload) and metadata
 * in an GenericDomainEventMessage and automatically assign the aggregate identifier and the next sequence number.
 */
class EventContainer implements Countable
{
    /**
     * @var GenericDomainEventMessage[]
     */
    private $events = [];

    /**
     * @var string
     */
    private $aggregateType;

    /**
     * @var mixed
     */
    private $aggregateId;

    /**
     * @var int
     */
    private $lastSequenceNumber;

    /**
     * @var int
     */
    private $lastCommittedSequenceNumber;

    /**
     * Initialize an EventContainer for an aggregate with the given aggregateIdentifier. This identifier will be
     * attached to all incoming events.
     *
     * @param string $aggregateType
     * @param mixed $aggregateId
     */
    public function __construct($aggregateType, $aggregateId)
    {
        $this->aggregateType = $aggregateType;
        $this->aggregateId   = $aggregateId;
    }

    /**
     * Add an event to this container.
     *
     * @param mixed $payload
     * @param Metadata|array $metadata
     * @return GenericDomainEventMessage
     */
    public function addEvent($payload, $metadata = null)
    {
        $domainEventMessage = new GenericDomainEventMessage(
            $this->aggregateType,
            $this->aggregateId,
            $this->newSequenceNumber(),
            $payload,
            $metadata
        );

        $this->addEventMessage($domainEventMessage);
        return $domainEventMessage;
    }

    /**
     * @param DomainEventMessageInterface $domainEventMessage
     * @return DomainEventMessageInterface
     * @throws InvalidArgumentException
     */
    public function addEventMessage(DomainEventMessageInterface $domainEventMessage)
    {
        if ($domainEventMessage->getAggregateType() !== $this->aggregateType) {
            throw new InvalidArgumentException(sprintf(
                'Trying to add an event message of aggregate %s to the event container of aggregate %s',
                $domainEventMessage->getAggregateType(),
                $this->aggregateType
            ));
        }

        if ($domainEventMessage->getAggregateId() === null) {
            $domainEventMessage = new GenericDomainEventMessage(
                $domainEventMessage->getAggregateType(),
                $this->aggregateId,
                $domainEventMessage->getSequenceNumber(),
                $domainEventMessage->getPayload(),
                $domainEventMessage->getMetadata(),
                $domainEventMessage->getId(),
                $domainEventMessage->getTimestamp()
            );
        }

        $this->lastSequenceNumber = $domainEventMessage->getSequenceNumber();
        $this->events[] = $domainEventMessage;
        return $domainEventMessage;
    }

    /**
     * @return GenericDomainEventMessage[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Clears the events in this container. The sequence number is not modified by this call.
     */
    public function commit()
    {
        $this->lastCommittedSequenceNumber = $this->getLastSequenceNumber();
        $this->events = [];
    }

    /**
     * Returns the number of events currently inside this container.
     *
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * Sets the first sequence number that should be assigned to an incoming event.
     *
     * @param int $lastKnownSequenceNumber
     * @throws RuntimeException
     */
    public function initializeSequenceNumber($lastKnownSequenceNumber)
    {
        if (count($this) !== 0) {
            throw new RuntimeException('Cannot set first sequence number if events have already been added');
        }
        $this->lastCommittedSequenceNumber = $lastKnownSequenceNumber;
    }

    /**
     * Returns the sequence number of the last committed event, or null if no events have been committed.
     *
     * @return int
     */
    public function getLastSequenceNumber()
    {
        if (count($this->events) === 0) {
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
