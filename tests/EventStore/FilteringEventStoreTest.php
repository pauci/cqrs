<?php

declare(strict_types=1);

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\EventStore\FilteringEventStore;
use CQRS\EventStore\MemoryEventStore;
use PHPUnit\Framework\TestCase;

class FilteringEventStoreTest extends TestCase
{
    public function testFiltering(): void
    {
        $eventStore = new MemoryEventStore();

        $filteringEventStore = new FilteringEventStore($eventStore, new SomeEventFilter());

        $validEvent   = new GenericEventMessage(new SomeEvent(), Metadata::from(['valid' => true]));
        $invalidEvent = new GenericEventMessage(new SomeEvent(), Metadata::from(['valid' => false]));

        $filteringEventStore->store($validEvent);
        $filteringEventStore->store($invalidEvent);

        $event = $eventStore->pop();
        self::assertSame($validEvent, $event);

        $event = $eventStore->pop();
        self::assertNull($event);
    }
}
