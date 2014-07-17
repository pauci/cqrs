<?php

namespace CQRSTest\Serializer;

use CQRS\Domain\Message\AbstractDomainEvent;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\Serializer\ReflectionSerializer;
use Rhumsaa\Uuid\Uuid;

class ReflectionSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $serializer = new ReflectionSerializer();

        $event = new SomeEvent(['foo' => 'bar'], new SomeAggregate());
        $data = $serializer->serialize($event, 'json');

        $this->assertEquals(
            '{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","id":{"php_class":"Rhumsaa\\\Uuid\\\Uuid","uuid":"'
            . $event->getId()
            . '"},"timestamp":{"php_class":"DateTimeImmutable","time":"'
            . $event->getTimestamp()->format('Y-m-d H:i:s.u')
            . '","timezone":"'
            . strtr($event->getTimestamp()->getTimezone()->getName(), ['/' => '\/'])
            . '"},"aggregate_type":"CQRSTest\\\Serializer\\\SomeAggregate","aggregate_id":4,"event_name":"Some","payload":{"foo":"bar"}}',
            $data
        );
    }

    public function testDeserialize()
    {
        $serializer = new ReflectionSerializer();

        $data = <<<JSON
{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","id":{"php_class":"Rhumsaa\\\Uuid\\\Uuid","uuid":"d97f7374-b4d9-418a-8dc7-dfda0bcb785a"},"timestamp":{"php_class":"DateTimeImmutable","time":"2014-07-17 16:37:52.404972","timezone":"Europe\\/Bratislava"},"aggregate_type":"CQRSTest\\\Serializer\\\SomeAggregate","aggregate_id":4,"event_name":"CustomName","payload":{"foo":"bar"}}
JSON;

        /** @var SomeEvent $event */
        $event = $serializer->deserialize('', $data, 'json');

        $this->assertInstanceOf(SomeEvent::class, $event);
        $this->assertInstanceOf(Uuid::class, $event->getId());
        $this->assertEquals('d97f7374-b4d9-418a-8dc7-dfda0bcb785a', (string) $event->getId());
        $this->assertEquals('CustomName', $event->getEventName());
        $this->assertEquals('bar', $event->foo);
    }
}

/**
 * @property-read mixed $foo
 */
class SomeEvent extends AbstractDomainEvent
{
    protected $foo;
}

class SomeAggregate extends AbstractAggregateRoot
{
    public function getId()
    {
        return 4;
    }
}
