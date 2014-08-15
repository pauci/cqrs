<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\EventHandling\EventInterface;
use DateTime;
use PHPUnit_Framework_TestCase;
use Rhumsaa\Uuid\Uuid;

class GenericDomainEventMessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFromDomainEvent()
    {
        $event = new SomeDomainEvent();

        $message = new GenericDomainEventMessage('SomeAggregate', 1234, 5, $event);

        $this->assertEquals('SomeAggregate', $message->getAggregateType());
        $this->assertEquals(1234, $message->getAggregateId());
        $this->assertEquals(5, $message->getSequenceNumber());
        $this->assertSame($event, $message->getPayload());
    }

    public function testReconstructUsingExistingData()
    {
        $metadata  = ['foo' => 'bar'];
        $id        = Uuid::uuid4();
        $timestamp = new DateTime();

        $message = new GenericDomainEventMessage('SomeAggregate', 1234, 5, new SomeDomainEvent(), $metadata, $id, $timestamp);

        $this->assertSame($id, $message->getId());
        $this->assertSame($timestamp, $message->getTimestamp());
        $this->assertEquals($metadata, $message->getMetadata());
    }
}

class SomeDomainEvent implements EventInterface
{}
