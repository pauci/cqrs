<?php

declare(strict_types=1);

namespace CQRSTest\Serializer;

use CQRS\Serializer\JsonSerializer;
use CQRSTest\Serializer\Model\FloatObject;
use CQRSTest\Serializer\Model\IntegerObject;
use CQRSTest\Serializer\Model\IntObject;
use CQRSTest\Serializer\Model\UuidObject;
use CQRSTest\Serializer\Model\StringObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class JsonSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $event = new SomeEvent();

        $jsonSerializer = new JsonSerializer();
        self::assertEquals('{}', $jsonSerializer->serialize($event));
    }

    public function testDeserialize(): void
    {
        $jsonSerializer = new JsonSerializer();

        $event = new TestEvent();
        self::assertEquals($event, $jsonSerializer->deserialize('{}', TestEvent::class));

        $event = new TestEventWithCustomConstructor(new SomeAggregate());
        self::assertEquals(
            $event,
            $jsonSerializer->deserialize('{"some_aggregate":4}', TestEventWithCustomConstructor::class)
        );

        $event = new SomeEvent3(
            UuidObject::fromUuid(Uuid::fromString('a49fd9b2-d989-4cef-98a3-e96d65c1dba4')),
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
                '5',
                IntegerObject::class
            )
        );

        self::assertEquals(
            IntObject::fromInt(5),
            $jsonSerializer->deserialize(
                '5',
                IntObject::class
            )
        );

        self::assertEquals(
            FloatObject::fromFloat(5.6),
            $jsonSerializer->deserialize(
                '5.6',
                FloatObject::class
            )
        );

        self::assertEquals(
            StringObject::fromString('foo'),
            $jsonSerializer->deserialize(
                '"foo"',
                StringObject::class
            )
        );
    }
}
