<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\Event\FailedToDeserializeEvent;
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
        $hybridSerializer = new HybridSerializer($jsonSerializer, [
            'Test\Event\OriginalClass' => 'CQRSTest\Serializer\TestEvent',
            'Test\Event\AnotherOriginalClass' => 'AnotherOriginalClass2',
        ]);

        $event = new TestEvent();
        self::assertEquals($event, $hybridSerializer->deserialize('{}', 'CQRSTest\Serializer\TestEvent'));

        $event = new TestEventWithCustomConstructor(new SomeAggregate());
        self::assertEquals(
            $event,
            $hybridSerializer->deserialize('{"some_aggregate":4}', 'CQRSTest\Serializer\TestEventWithCustomConstructor')
        );

        $event = new TestEvent();
        self::assertEquals($event, $hybridSerializer->deserialize('{}', 'Test\Event\OriginalClass'));

        $event = new FailedToDeserializeEvent(
            sprintf('Class %s not found', 'AnotherOriginalClass2'),
            'AnotherOriginalClass2',
            '{}'
        );
        self::assertEquals($event, $hybridSerializer->deserialize('{}', 'Test\Event\AnotherOriginalClass'));

        $event = new FailedToDeserializeEvent(
            sprintf('Class %s not found', 'Test\Event\AnotherOriginalClass3'),
            'Test\Event\AnotherOriginalClass3',
            '{}'
        );
        self::assertEquals($event, $hybridSerializer->deserialize('{}', 'Test\Event\AnotherOriginalClass3'));

    }
} 
