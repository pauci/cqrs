<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Domain\Message\Metadata;
use DateTimeImmutable;
use Pauci\DateTime\DateTime;
use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\Uuid;

class GenericEventMessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFromEvent()
    {
        $event = new SomePayload();
        $eventMessage = new GenericEventMessage($event);

        $this->assertSame($event, $eventMessage->getPayload());
        $this->assertInstanceOf(DateTimeImmutable::class, $eventMessage->getTimestamp());
    }

    public function testReconstructUsingExistingData()
    {
        $metadata = Metadata::from(['foo' => 'bar']);
        $id = Uuid::uuid4();
        $timestamp = DateTime::microsecondsNow();

        $eventMessage = new GenericEventMessage(new SomePayload(), $metadata, $id, $timestamp);

        $this->assertSame($timestamp, $eventMessage->getTimestamp());
        $this->assertSame($id, $eventMessage->getId());
        $this->assertSame($metadata, $eventMessage->getMetadata());
    }
}
