<?php

declare(strict_types=1);

namespace CQRSTest\Serializer;

use CQRS\Serializer\Event\FailedToDeserializeEvent;
use CQRS\Serializer\HybridSerializer;
use CQRS\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;

class HybridSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $event = new SomeEvent();

        $jsonSerializer = new JsonSerializer();
        $hybridSerializer = new HybridSerializer($jsonSerializer, []);

        self::assertEquals('{}', $hybridSerializer->serialize($event));
    }

    public function testDeserialize(): void
    {
        $jsonSerializer = new JsonSerializer();
        $hybridSerializer = new HybridSerializer($jsonSerializer, [
            'Test\Event\OriginalClass' => TestEvent::class,
            'Test\Event\AnotherOriginalClass' => 'AnotherOriginalClass2',
            'Test\Event\AnotherOriginalClass4' => SomeEvent2::class
        ]);

        $event = new TestEvent();
        self::assertEquals($event, $hybridSerializer->deserialize('{}', TestEvent::class));

        $event = new TestEventWithCustomConstructor(new SomeAggregate());
        self::assertEquals(
            $event,
            $hybridSerializer->deserialize('{"some_aggregate":4}', TestEventWithCustomConstructor::class)
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
            SomeEvent2::class,
            '{"data":["one","two"]}'
        );
        self::assertEquals(
            $event,
            $hybridSerializer->deserialize('{"data":["one","two"]}', 'Test\Event\AnotherOriginalClass4')
        );
    }
}
