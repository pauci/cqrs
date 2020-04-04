<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use Pauci\DateTime\DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class GenericDomainEventMessage extends GenericEventMessage implements DomainEventMessageInterface
{
    private string $aggregateType;

    /**
     * @var mixed
     */
    private $aggregateId;

    private int $sequenceNumber;

    /**
     * @param mixed $aggregateId
     * @param mixed $payload
     * @param Metadata|array|null $metadata
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

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['aggregateType'] = $this->aggregateType;
        $data['aggregateId'] = $this->aggregateId;
        return $data;
    }

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

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }
}
