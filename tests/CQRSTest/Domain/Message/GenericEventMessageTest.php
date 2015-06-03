<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericEventMessage;
use DateTime;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;
use Rhumsaa\Uuid\Uuid;

class GenericEventMessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFromEvent()
    {
        $event = new SomeEvent();
        $eventMessage = new GenericEventMessage($event);

        $this->assertSame($event, $eventMessage->getPayload());
        $this->assertInstanceOf(DateTimeImmutable::class, $eventMessage->getTimestamp());
    }

    public function testReconstructUsingExistingData()
    {
        $metadata  = ['foo' => 'bar'];
        $id        = Uuid::uuid4();
        $timestamp = new DateTime();

        $eventMessage = new GenericEventMessage(new SomeEvent(), $metadata, $id, $timestamp);

        $this->assertSame($timestamp, $eventMessage->getTimestamp());
        $this->assertSame($id, $eventMessage->getId());
        $this->assertEquals($metadata, $eventMessage->getMetadata()->toArray());
    }
}

class SomeEvent
{}
