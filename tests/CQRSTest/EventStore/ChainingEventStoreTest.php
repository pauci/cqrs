<?php

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventStore\ChainingEventStore;
use CQRS\EventStore\MemoryEventStore;

class ChainingEventStoreTest extends \PHPUnit_Framework_TestCase
{
    public function testStoreEvent()
    {
        $es1 = new MemoryEventStore();
        $es2 = new MemoryEventStore();

        $chainingEventStore = new ChainingEventStore([$es1, $es2]);

        $event = new GenericEventMessage(new SomeEvent());
        $chainingEventStore->store($event);

        $events1 = $es1->read();
        $this->assertEquals([$event], $events1);

        $events2 = $es2->read();
        $this->assertEquals([$event], $events2);
    }
}
