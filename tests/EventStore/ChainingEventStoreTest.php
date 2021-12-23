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

        $es1event = $es1->pop();
        self::assertSame($event, $es1event);

        $es2event = $es2->pop();
        self::assertSame($event, $es2event);
    }
}
