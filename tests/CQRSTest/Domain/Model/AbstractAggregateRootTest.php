<?php

namespace CQRSTest\Domain\Model;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\EventHandling\EventInterface;
use PHPUnit_Framework_TestCase;

class AbstractAggregateRootTest extends PHPUnit_Framework_TestCase
{
    public function testRaiseAndPullDomainEvents()
    {
        $event = new SomeDomainEvent();

        $aggregateRoot = new AggregateRootUnderTest();
        $aggregateRoot->raise($event);

        $eventMessages = $aggregateRoot->pullDomainEvents();

        $this->assertCount(1, $eventMessages);
        $this->assertInstanceOf(GenericDomainEventMessage::class, $eventMessages[0]);
        $this->assertEquals(AggregateRootUnderTest::class, $eventMessages[0]->getAggregateType());
        $this->assertEquals(4, $eventMessages[0]->getAggregateId());
        $this->assertSame($event, $eventMessages[0]->getPayload());

        $this->assertEmpty($aggregateRoot->pullDomainEvents());
    }
}

class AggregateRootUnderTest extends AbstractAggregateRoot
{
    public function raise($event)
    {
        $this->raiseDomainEvent($event);
    }

    public function &getId()
    {
        $id = 4;
        return $id;
    }
}

class SomeDomainEvent implements EventInterface
{}
