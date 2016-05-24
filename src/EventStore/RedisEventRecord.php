<?php

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
    /**
     * @var string
     */
    private $data;

    /**
     * @param EventMessageInterface $event
     * @param SerializerInterface $serializer
     * @return RedisEventRecord
     */
    public static function fromMessage(EventMessageInterface $event, SerializerInterface $serializer)
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

        return new self(json_encode($data));
    }

    /**
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->data, true);
    }

    /**
     * @param SerializerInterface $serializer
     * @return GenericDomainEventMessage|GenericEventMessage
     */
    public function toMessage(SerializerInterface $serializer)
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
