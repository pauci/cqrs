<?php

declare(strict_types=1);

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventStore\RedisEventStore;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Redis;

class RedisEventStoreTest extends TestCase
{
    private Redis $redis;

    private RedisEventStore $redisEventStore;

    public function setUp(): void
    {
        if (!extension_loaded('redis')) {
            self::markTestSkipped('The redis extension is not available.');
        }

        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1');
        $this->redis->del('cqrs_event');
        $this->redisEventStore = new RedisEventStore(new SomeSerializer(), $this->redis, size: 4);
    }

    /**
     * @dataProvider getData
     */
    public function testStoreEvent(EventMessageInterface $event, string $record): void
    {
        $this->redisEventStore->store($event);

        $data = $this->redis->lRange('cqrs_event', 0, -1);

        self::assertCount(1, $data);
        self::assertJsonStringEqualsJsonString($record, $data[0]);
    }

    public function testCappedCollection(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $event = new GenericEventMessage(new SomeEvent());
            $this->redisEventStore->store($event);
        }

        $records = $this->redis->lRange('cqrs_event', 0, 10);
        self::assertCount(4, $records);
    }

    public function testPopEvent(): void
    {
        $data = $this->getData();

        foreach ($data as $record) {
            $this->redis->lPush('cqrs_event', $record[1]);
        }

        foreach ($data as $record) {
            $event = $this->redisEventStore->pop()->toMessage(new SomeSerializer());
            self::assertEquals($record[0], $event);
        }

        self::assertFalse((bool) $this->redis->exists('cqrs_event'));
    }

    public function getData(): array
    {
        $aggregateId = 123;

        return [
            [
                new GenericEventMessage(
                    new SomeEvent(),
                    [],
                    Uuid::fromString('777bb61d-b9fa-4023-937e-1b6e4fc9f7b4'),
                    new DateTimeImmutable('2015-02-11T15:23:42.195819+0100')
                ),
                <<<'JSON'
                {
                  "id": "777bb61d-b9fa-4023-937e-1b6e4fc9f7b4",
                  "timestamp": "2015-02-11T15:23:42.195819+01:00",
                  "payload": {
                    "data": "{}",
                    "type": "CQRSTest\\EventStore\\SomeEvent"
                  },
                  "metadata": {
                    "data": {},
                    "types": {}
                  }
                }
                JSON,
            ],
            [
                new GenericDomainEventMessage(
                    'SomeAggregate',
                    $aggregateId,
                    4,
                    new SomeEvent(),
                    [],
                    Uuid::fromString('eabd641e-4181-4b5f-b191-ecdd40d82b1b'),
                    new DateTimeImmutable('2015-02-11T13:40:29.658819+0100')
                ),
                <<<'JSON'
                {
                  "id": "eabd641e-4181-4b5f-b191-ecdd40d82b1b",
                  "timestamp": "2015-02-11T13:40:29.658819+01:00",
                  "payload": {
                    "data": "{}",
                    "type": "CQRSTest\\EventStore\\SomeEvent"
                  },
                  "metadata": {
                    "data": {},
                    "types": {}
                  },
                  "aggregate": {
                    "type": "SomeAggregate",
                    "id": 123,
                    "seq": 4
                  }
                }
                JSON
            ],
        ];
    }
}
