<?php

namespace CQRSTest\Serializer;

use CQRS\Domain\Message\AbstractDomainEvent;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\Serializer\ReflectionSerializer;

class ReflectionSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $event = new SomeEvent(['foo' => 'bar'], new SomeAggregate());

        $serializer = new ReflectionSerializer();
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
}

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
