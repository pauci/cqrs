<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Serializer\SerializerInterface;
use Redis;

class RedisEventStore implements EventStoreInterface
{
    private SerializerInterface $serializer;

    private Redis $redis;

    private string $key;

    private int $size;

    public function __construct(
        SerializerInterface $serializer,
        Redis $redis,
        string $key = 'cqrs_event',
        int $size = 0
    ) {
        $this->serializer = $serializer;
        $this->redis = $redis;
        $this->key = $key;
        $this->size = $size;
    }

    public function store(EventMessageInterface $event): void
    {
        $record = RedisEventRecord::fromMessage($event, $this->serializer);
        $this->redis->lPush($this->key, (string) $record);

        if ($this->size > 0) {
            $this->redis->lTrim($this->key, 0, $this->size - 1);
        }
    }

    public function pop(int $timeout = 0): ?RedisEventRecord
    {
        $data = $this->redis->brPop($this->key, $timeout);

        if (!array_key_exists(1, $data)) {
            return null;
        }

        return new RedisEventRecord($data[1]);
    }
}
