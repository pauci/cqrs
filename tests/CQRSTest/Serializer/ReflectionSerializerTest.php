<?php

namespace CQRSTest\Serializer;

use CQRS\Domain\Message\AbstractDomainEvent;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\Serializer\ReflectionSerializer;
use DateTime;
use PHPUnit_Framework_TestCase;
use Rhumsaa\Uuid\Uuid;
use stdClass;

class ReflectionSerializerTest extends PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $serializer = new ReflectionSerializer();

        $event = new SomeEvent([
            'foo'    => 'bar',
            'nested' => new stdClass(),
        ], new SomeAggregate());
        $data = $serializer->serialize($event, 'json');

        $this->assertEquals(
            '{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","id":{"php_class":"Rhumsaa\\\Uuid\\\Uuid","uuid":"'
            . $event->getId()
            . '"},"timestamp":{"php_class":"DateTimeImmutable","time":"'
            . $event->getTimestamp()->format('Y-m-d H:i:s.u')
            . '","timezone":"'
            . strtr($event->getTimestamp()->getTimezone()->getName(), ['/' => '\/'])
            . '"},"aggregate_type":"CQRSTest\\\Serializer\\\SomeAggregate","aggregate_id":4,"event_name":"Some","payload":{"foo":"bar","time":null,"nested":{"php_class":"stdClass"}}}',
            $data
        );
    }

    public function testDeserialize()
    {
        $serializer = new ReflectionSerializer();

        $data = <<<JSON
{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","id":{"php_class":"Rhumsaa\\\Uuid\\\Uuid","uuid":"d97f7374-b4d9-418a-8dc7-dfda0bcb785a"},"timestamp":{"php_class":"DateTimeImmutable","time":"2014-07-17 16:37:52.404972","timezone":"Europe\\/Bratislava"},"aggregate_type":"CQRSTest\\\Serializer\\\SomeAggregate","aggregate_id":4,"event_name":"CustomName","payload":{"foo":"bar","time":{"php_class":"DateTime","time":"2014-08-08 13:39:15.000000","timezone_type":3,"timezone":"Europe\/Paris"}}}
JSON;

        /** @var SomeEvent $event */
        $event = $serializer->deserialize($data, '', 'json');

        $this->assertInstanceOf(SomeEvent::class, $event);
        $this->assertInstanceOf(Uuid::class, $event->getId());
        $this->assertEquals('d97f7374-b4d9-418a-8dc7-dfda0bcb785a', (string) $event->getId());
        $this->assertEquals('CustomName', $event->getEventName());
        $this->assertEquals('bar', $event->foo);
        $this->assertEquals('2014-08-08 13:39:15', $event->time->format('Y-m-d H:i:s'));
    }
}

/**
 * @property-read string $foo
 * @property-read DateTime $time
 */
class SomeEvent extends AbstractDomainEvent
{
    protected $foo;

    protected $time;

    protected $nested;
}

class SomeAggregate extends AbstractAggregateRoot
{
    public function getId()
    {
        return 4;
    }
}
