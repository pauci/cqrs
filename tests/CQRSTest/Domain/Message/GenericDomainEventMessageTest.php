<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Domain\Message\Timestamp;
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
        $timestamp   = new Timestamp();
        $metadata    = Metadata::from(['foo' => 'bar']);

        $message = new GenericDomainEventMessage('SomeAggregate', $aggregateId, 5, new SomeDomainEvent(), $metadata, $id, $timestamp);

        $this->assertSame($metadata, $message->getMetadata());
        $this->assertSame($id, $message->getId());
        $this->assertSame($timestamp, $message->getTimestamp());
    }
}

class SomeDomainEvent
{}
