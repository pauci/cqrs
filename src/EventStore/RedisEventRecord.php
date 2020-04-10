<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Serializer\SerializerInterface;
use Pauci\DateTime\DateTime;
use Ramsey\Uuid\Uuid;

class RedisEventRecord
{
    private string $data;

    public static function fromMessage(EventMessageInterface $event, SerializerInterface $serializer): RedisEventRecord
    {
        $data = [
            'id' => $event->getId(),
            'timestamp' => $event->getTimestamp(),
            'payload_type' => $event->getPayloadType(),
            'payload' => $serializer->serialize($event->getPayload()),
            'metadata' => $serializer->serialize($event->getMetadata()),
        ];

        if ($event instanceof DomainEventMessageInterface) {
            $data['aggregate'] = [
                'type' => $event->getAggregateType(),
                'id' => $event->getAggregateId(),
                'seq' => $event->getSequenceNumber(),
            ];
        }

        return new self(json_encode($data, JSON_THROW_ON_ERROR, 512));
    }

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return json_decode($this->data, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return GenericDomainEventMessage|GenericEventMessage
     */
    public function toMessage(SerializerInterface $serializer): GenericEventMessage
    {
        $data = $this->toArray();

        $id = Uuid::fromString($data['id']);
        $timestamp = DateTime::fromString($data['timestamp']);
        $payload = $serializer->deserialize($data['payload'], $data['payload_type']);
        $metadata = $serializer->deserialize($data['metadata'], Metadata::class);

        if (array_key_exists('aggregate', $data)) {
            return new GenericDomainEventMessage(
                $data['aggregate']['type'],
                $data['aggregate']['id'],
                $data['aggregate']['seq'],
                $payload,
                $metadata,
                $id,
                $timestamp
            );
        }

        return new GenericEventMessage($payload, $metadata, $id, $timestamp);
    }
}
