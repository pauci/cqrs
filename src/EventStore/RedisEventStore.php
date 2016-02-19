<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Exception;
use CQRS\Serializer\SerializerInterface;
use Ramsey\Uuid\UuidInterface;
use Redis;
use Traversable;

class RedisEventStore implements EventStoreInterface
{
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
     * @var int
     */
    private $size;

    /**
     * @param SerializerInterface $serializer
     * @param Redis $redis
     * @param string|null $key
     * @param int|null $size
     */
    public function __construct(SerializerInterface $serializer, Redis $redis, $key = null, $size = null)
    {
        $this->serializer = $serializer;
        $this->redis = $redis;

        if (null !== $key) {
            $this->key = $key;
        }

        if (null !== $size) {
            $this->size = $size;
        }
    }

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        $record = RedisEventRecord::fromMessage($event, $this->serializer);
        $this->redis->lPush($this->key, (string) $record);

        if ($this->size > 0) {
            $this->redis->lTrim($this->key, 0, $this->size - 1);
        }
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

        return array_map(function ($data) {
            $record = new RedisEventRecord($data);
            return $record->toMessage($this->serializer);
        }, $records);
    }

    /**
     * @param int $timeout
     * @return RedisEventRecord|null
     */
    public function pop($timeout = 0)
    {
        $data = $this->redis->brPop($this->key, (int) $timeout);

        if (!array_key_exists(1, $data)) {
            return null;
        }

        return new RedisEventRecord($data[1]);
    }

    /**
     * @param null|UuidInterface $previousEventId
     * @return Traversable
     */
    public function iterate(UuidInterface $previousEventId = null)
    {
        throw new Exception\BadMethodCallException('Method is not implemented');
    }
}
