<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use Pauci\DateTime\DateTimeInterface;
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
     * @param mixed $payload
     * @param Metadata|array|null $metadata
     * @param UuidInterface|null $id
     * @param DateTimeInterface|null $timestamp
     */
    public function __construct(
        string $aggregateType,
        $aggregateId,
        int $sequenceNumber,
        $payload,
        $metadata = null,
        UuidInterface $id = null,
        DateTimeInterface $timestamp = null
    ) {
        $this->aggregateType = $aggregateType;
        $this->aggregateId = $aggregateId;
        $this->sequenceNumber = $sequenceNumber;

        parent::__construct($payload, $metadata, $id, $timestamp);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['aggregateType'] = $this->aggregateType;
        $data['aggregateId'] = $this->aggregateId;
        return $data;
    }

    /**
     * @return string
     */
    public function getAggregateType(): string
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
    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }
}
