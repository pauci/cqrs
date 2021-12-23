<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Serializer\SerializerInterface;
use Pauci\DateTime\DateTime;
use Ramsey\Uuid\Uuid;

class RedisEventRecord
{
    private string $data;

    public static function fromMessage(EventMessageInterface $event, SerializerInterface $serializer): self
    {
        $metadata = $event->getMetadata()->toArray();
        $metadataTypes = [];

        foreach ($metadata as $key => $value) {
            if (is_object($value)) {
                $metadataTypes[$key] = get_class($value);
                $metadata[$key] = $serializer->serialize($value);
            }
        }

        $data = [
            'id' => $event->getId(),
            'timestamp' => $event->getTimestamp(),
            'payload' => [
                'data' => $serializer->serialize($event->getPayload()),
                'type' => $event->getPayloadType(),
            ],
            'metadata' => [
                'data' => (object) $metadata,
                'types' => (object) $metadataTypes,
            ],
        ];

        if ($event instanceof DomainEventMessageInterface) {
            $data['aggregate'] = [
                'type' => $event->getAggregateType(),
                'id' => $event->getAggregateId(),
                'seq' => $event->getSequenceNumber(),
            ];
        }

        return new self(json_encode($data, JSON_THROW_ON_ERROR));
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
        return (array) json_decode($this->data, true, 512, JSON_THROW_ON_ERROR);
    }

    public function toMessage(SerializerInterface $serializer): GenericEventMessage|GenericDomainEventMessage
    {
        $data = $this->toArray();

        $id = Uuid::fromString($data['id']);
        $timestamp = DateTime::fromString($data['timestamp']);
        $payload = $serializer->deserialize($data['payload']['data'], $data['payload']['type']);

        $metadata = $data['metadata']['data'];
        foreach ($data['metadata']['types'] as $key => $type) {
            $metadata[$key] = $serializer->deserialize($metadata[$key], $type);
        }

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
