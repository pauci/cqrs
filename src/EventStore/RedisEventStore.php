<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Serializer\SerializerInterface;
use Redis;

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
        $record = RedisEventRecord::fromMessage($event, $this->serializer);
        $this->redis->lPush($this->key, (string) $record);
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

        return array_map(function($data) {
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

        if (!isset($data[1])) {
            return null;
        }

        return new RedisEventRecord($data[1]);
    }
}
