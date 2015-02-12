<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventHandling\EventInterface;
use CQRS\Serializer\SerializerInterface;
use Redis;
use Rhumsaa\Uuid\Uuid;

class RedisEventStore implements EventStoreInterface
{
    const TIMESTAMP_FORMAT = 'Y-m-d\TH:i:s.uO';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $key = 'cqrs_event';

    /**
     * @param SerializerInterface $serializer
     * @param Redis $redis
     * @param string|null $key
     */
    public function __construct(SerializerInterface $serializer, Redis $redis, $key = null)
    {
        $this->serializer = $serializer;
        $this->redis      = $redis;

        if (null !== $key) {
            $this->key = $key;
        }
    }

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        $data = $this->encode($event);
        $this->redis->lPush($this->key, $data);
    }

    /**
     * @param int|null $offset
     * @param int $limit
     * @return EventMessageInterface[]
     */
    public function read($offset = null, $limit = 10)
    {
        if (null == $offset) {
            $offset = -10;
        }

        $records = $this->redis->lRange($this->key, $offset, $limit);

        return array_map(function($record) {
            return $this->decode($record);
        }, $records);
    }

    /**
     * @return GenericDomainEventMessage|GenericEventMessage|null
     */
    public function pop()
    {
        $data = $this->redis->brPop($this->key, 0);

        if (!isset($data[1])) {
            return null;
        }

        return $this->decode($data[1]);
    }

    /**
     * @param EventMessageInterface $event
     * @return string
     */
    private function encode(EventMessageInterface $event)
    {
        $data = [
            'id'           => (string) $event->getId(),
            'timestamp'    => $event->getTimestamp()->format(self::TIMESTAMP_FORMAT),
            'payload_type' => $event->getPayloadType(),
            'payload'      => $this->serializer->serialize($event->getPayload(), 'json'),
            'metadata'     => $this->serializer->serialize($event->getMetadata(), 'json')
        ];

        if ($event instanceof DomainEventMessageInterface) {
            $data['aggregate'] = [
                'type' => $event->getAggregateType(),
                'id'   => $event->getAggregateId(),
                'seq'  => $event->getSequenceNumber()
            ];
        }

        return json_encode($data);
    }

    /**
     * @param string $data
     * @return GenericDomainEventMessage|GenericEventMessage
     */
    private function decode($data)
    {
        $data = json_decode($data, true);

        /** @var EventInterface $payload */
        $id        = Uuid::fromString($data['id']);
        $timestamp = \DateTimeImmutable::createFromFormat(self::TIMESTAMP_FORMAT, $data['timestamp']);
        $payload   = $this->serializer->deserialize($data['payload'], $data['payload_type'], 'json');
        $metadata  = $this->serializer->deserialize($data['metadata'], 'array', 'json');

        if (isset($data['aggregate'])) {
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
