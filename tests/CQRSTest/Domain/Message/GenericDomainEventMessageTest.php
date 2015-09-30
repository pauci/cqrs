<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericDomainEventMessage;
use DateTime;
use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\Uuid;

class GenericDomainEventMessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFromDomainEvent()
    {
        $aggregateId = 1234;
        $event       = new SomeDomainEvent();

        $message = new GenericDomainEventMessage('SomeAggregate', $aggregateId, 5, $event);

        $this->assertEquals('SomeAggregate', $message->getAggregateType());
        $this->assertEquals(1234, $message->getAggregateId());
        $this->assertEquals(5, $message->getSequenceNumber());
        $this->assertSame($event, $message->getPayload());
    }

    public function testReconstructUsingExistingData()
    {
        $aggregateId = 1234;
        $id          = Uuid::uuid4();
        $timestamp   = new DateTime();

        $message = new GenericDomainEventMessage('SomeAggregate', $aggregateId, 5, new SomeDomainEvent(), ['foo' => 'bar'], $id, $timestamp);

        $this->assertSame($id, $message->getId());
        $this->assertSame($timestamp, $message->getTimestamp());
        $this->assertEquals(['foo' => 'bar'], $message->getMetadata()->toArray());
    }
}

class SomeDomainEvent
{}
