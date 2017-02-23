<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\HybridSerializer;
use CQRS\Serializer\JsonSerializer;

class HybridSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $event = new SomeEvent();

        $jsonSerializer = new JsonSerializer();
        $hybridSerializer = new HybridSerializer($jsonSerializer, []);

        self::assertEquals('{}', $hybridSerializer->serialize($event));
    }

    public function testDeserialize()
    {
        $jsonSerializer = new JsonSerializer();
        $hybridSerializer = new HybridSerializer($jsonSerializer, []);

        $event = new TestEvent();
        self::assertEquals($event, $hybridSerializer->deserialize('{}', 'CQRSTest\Serializer\TestEvent'));

        $event = new TestEventWithCustomConstructor(new SomeAggregate());
        self::assertEquals(
            $event,
            $hybridSerializer->deserialize('{"some_aggregate":4}', 'CQRSTest\Serializer\TestEventWithCustomConstructor')
        );

    }
} 
