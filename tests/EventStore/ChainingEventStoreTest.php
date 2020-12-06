<?php

declare(strict_types=1);

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventStore\ChainingEventStore;
use CQRS\EventStore\MemoryEventStore;
use PHPUnit\Framework\TestCase;

class ChainingEventStoreTest extends TestCase
{
    public function testStoreEvent(): void
    {
        $es1 = new MemoryEventStore();
        $es2 = new MemoryEventStore();

        $chainingEventStore = new ChainingEventStore([$es1, $es2]);

        $event = new GenericEventMessage(new SomeEvent());
        $chainingEventStore->store($event);

        $events1 = $es1->read();
        self::assertEquals([$event], $events1);

        $events2 = $es2->read();
        self::assertEquals([$event], $events2);
    }
}
