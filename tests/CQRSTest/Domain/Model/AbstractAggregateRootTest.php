<?php

namespace CQRSTest\Domain\Model;

use CQRS\Domain\Message\GenericDomainEventMessage;
use PHPUnit_Framework_TestCase;

class AbstractAggregateRootTest extends PHPUnit_Framework_TestCase
{
    public function testRegisterEvent()
    {
        $event = new SomeEvent();

        $aggregateRoot = new AggregateRootUnderTest(4);
        $aggregateRoot->raise($event);

        $eventMessages = $aggregateRoot->getUncommittedEvents();

        $this->assertCount(1, $eventMessages);
        $this->assertInstanceOf(GenericDomainEventMessage::class, $eventMessages[0]);
        $this->assertEquals(AggregateRootUnderTest::class, $eventMessages[0]->getAggregateType());
        $this->assertEquals(4, $eventMessages[0]->getAggregateId());
        $this->assertSame($event, $eventMessages[0]->getPayload());

        $aggregateRoot->commitEvents();
        $this->assertEmpty($aggregateRoot->getUncommittedEvents());
    }

    public function testRegisterEventInjectsAggregateId()
    {
        $domainEvent = new SomeDomainEvent();

        $aggregateRoot = new AggregateRootUnderTest(4);
        $aggregateRoot->raise($domainEvent);

        $this->assertEquals(4, $domainEvent->aggregateId);
    }
}
