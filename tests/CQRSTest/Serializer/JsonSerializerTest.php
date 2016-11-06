<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\JsonSerializer;

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

    }
} 
