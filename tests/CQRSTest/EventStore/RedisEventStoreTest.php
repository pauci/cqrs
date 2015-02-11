<?php

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventStore\RedisEventStore;
use CQRS\Serializer\SerializerInterface;
use DateTimeImmutable;
use Redis;
use Rhumsaa\Uuid\Uuid;

class RedisEventStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var RedisEventStore
     */
    private $redisEventStore;

    public function setUp()
    {
        $serializer = $this->getMock(SerializerInterface::class);
        $serializer->expects($this->any())
            ->method('serialize')
            ->will($this->returnValue('{}'));
        $serializer->expects($this->any())
            ->method('deserialize')
            ->will($this->returnValueMap([
                ['{}', SomeEvent::class, 'json', new SomeEvent()],
                ['{}', 'array', 'json', []]
            ]));
        /** @var SerializerInterface $serializer */

        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1');
        $this->redis->del('cqrs_event');
        $this->redisEventStore = new RedisEventStore($serializer, $this->redis);
    }

    /**
     * @dataProvider getData
     * @param EventMessageInterface $event
     * @param string $record
     */
    public function testStoreEvent(EventMessageInterface $event, $record)
    {
        $this->redisEventStore->store($event);

        $data = $this->redis->lRange('cqrs_event', 0, -1);

        $this->assertCount(1, $data);
        $this->assertEquals($record, $data[0]);
    }

    /**
     * @dataProvider getData
     * @param EventMessageInterface $event
     * @param string $record
     */
    public function testReadEvents(EventMessageInterface $event, $record)
    {
        $this->redis->lPush('cqrs_event', $record);

        $events = $this->redisEventStore->read();

        $this->assertCount(1, $events);
        $this->assertEquals($event, $events[0]);
    }

    public function testPopEvent()
    {
        $data = $this->getData();

        foreach ($data as $record) {
            $this->redis->lPush('cqrs_event', $record[1]);
        }

        foreach ($data as $record) {
            $event = $this->redisEventStore->pop();
            $this->assertEquals($record[0], $event);
        }

        $this->assertFalse($this->redis->exists('cqrs_event'));
    }

    public function getData()
    {
        $aggregateId = 123;

        return [
            [
                new GenericEventMessage(new SomeEvent(), null, Uuid::fromString('777bb61d-b9fa-4023-937e-1b6e4fc9f7b4'), new DateTimeImmutable('2015-02-11T15:23:42.195819+0100')),
                '{"id":"777bb61d-b9fa-4023-937e-1b6e4fc9f7b4","timestamp":"2015-02-11T15:23:42.195819+0100","payload_type":"CQRSTest\\\EventStore\\\SomeEvent","payload":"{}","metadata":"{}"}'
            ],
            [
                new GenericDomainEventMessage('SomeAggregate', $aggregateId, 4, new SomeEvent(), null, Uuid::fromString('eabd641e-4181-4b5f-b191-ecdd40d82b1b'), new DateTimeImmutable('2015-02-11T13:40:29.658819+0100')),
                '{"id":"eabd641e-4181-4b5f-b191-ecdd40d82b1b","timestamp":"2015-02-11T13:40:29.658819+0100","payload_type":"CQRSTest\\\EventStore\\\SomeEvent","payload":"{}","metadata":"{}","aggregate":{"type":"SomeAggregate","id":123,"seq":4}}'
            ]
        ];
    }
}
