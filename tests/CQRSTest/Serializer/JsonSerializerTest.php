<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\JsonSerializer;
use CQRSTest\Serializer\Jms\FloatObject;
use CQRSTest\Serializer\Jms\IntegerObject;
use CQRSTest\Serializer\Jms\IntObject;
use CQRSTest\Serializer\Jms\ObjectWithUuid;
use CQRSTest\Serializer\Jms\StringObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class JsonSerializerTest extends TestCase
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
            IntegerObject::fromInteger(5),
            StringObject::fromString('some string')
        );
        self::assertEquals(
            $event,
            $jsonSerializer->deserialize(
                '{"uuid":"a49fd9b2-d989-4cef-98a3-e96d65c1dba4", "int":5, "string1":"some string", "string2":null}',
                SomeEvent3::class
            )
        );

        self::assertEquals(
            IntegerObject::fromInteger(5),
            $jsonSerializer->deserialize(
                5,
                IntegerObject::class
            )
        );

        self::assertEquals(
            IntObject::fromInt(5),
            $jsonSerializer->deserialize(
                5,
                IntObject::class
            )
        );

        self::assertEquals(
            FloatObject::fromFloat(5.6),
            $jsonSerializer->deserialize(
                5.6,
                FloatObject::class
            )
        );

        self::assertEquals(
            StringObject::unknown(),
            $jsonSerializer->deserialize(
                null,
                StringObject::class
            )
        );

    }
} 
