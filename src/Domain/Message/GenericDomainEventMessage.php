<?php

namespace CQRS\Domain\Message;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class GenericDomainEventMessage extends GenericEventMessage implements DomainEventMessageInterface
{
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
    private $sequenceNumber;

    /**
     * @param string $aggregateType
     * @param mixed $aggregateId
     * @param int $sequenceNumber
     * @param object $payload
     * @param Metadata|array $metadata
     * @param UuidInterface|null $id
     * @param DateTimeInterface $timestamp
     */
    public function __construct($aggregateType, $aggregateId, $sequenceNumber, $payload, $metadata = null, UuidInterface $id = null, DateTimeInterface $timestamp = null)
    {
        $this->aggregateType = $aggregateType;
        $this->aggregateId = $aggregateId;
        $this->sequenceNumber = $sequenceNumber;

        parent::__construct($payload, $metadata, $id, $timestamp);
    }

    /**
     * @return string
     */
    public function getAggregateType()
    {
        return $this->aggregateType;
    }

    /**
     * @return mixed
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    /**
     * @return int
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }
}
