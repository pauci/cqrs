<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\Event\FailedToDeserializeEvent;
use CQRS\Serializer\HybridSerializer;
use CQRS\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;

class HybridSerializerTest extends TestCase
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
            'Test\Event\AnotherOriginalClass4' => SomeEvent2::class
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
            'ReflectionException',
            'CQRSTest\Serializer\SomeEvent2',
            '{"data":["one","two"]}'
        );
        self::assertEquals($event, $hybridSerializer->deserialize('{"data":["one","two"]}', 'Test\Event\AnotherOriginalClass4'));

    }
} 
