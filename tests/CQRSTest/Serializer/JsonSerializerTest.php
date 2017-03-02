<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\JsonSerializer;
use CQRSTest\Serializer\Jms\IntegerObject;
use CQRSTest\Serializer\Jms\ObjectWithUuid;
use Ramsey\Uuid\Uuid;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $event = new SomeEvent();

        $jsonSerializer = new JsonSerializer();
        self::assertEquals('{}', $jsonSerializer->serialize($event));
    }

    public function testDeserialize()
    {
        $jsonSerializer = new JsonSerializer();

        $event = new TestEvent();
        self::assertEquals($event, $jsonSerializer->deserialize('{}', 'CQRSTest\Serializer\TestEvent'));

        $event = new TestEventWithCustomConstructor(new SomeAggregate());
        self::assertEquals(
            $event,
            $jsonSerializer->deserialize('{"some_aggregate":4}', 'CQRSTest\Serializer\TestEventWithCustomConstructor')
        );

        $event = new SomeEvent3(
            ObjectWithUuid::fromUuid(Uuid::fromString('a49fd9b2-d989-4cef-98a3-e96d65c1dba4')),
            IntegerObject::fromInteger(5)
        );
        self::assertEquals(
            $event,
            $jsonSerializer->deserialize('{"uuid":"a49fd9b2-d989-4cef-98a3-e96d65c1dba4", "int":5}', SomeEvent3::class)
        );

    }
} 
