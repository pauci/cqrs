<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use Pauci\DateTime\DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GenericDomainEventMessageTest extends TestCase
{
    public function testCreateFromDomainEvent(): void
    {
        $event = new SomePayload();

        $message = new GenericDomainEventMessage('SomeAggregate', 1234, 5, $event);

        self::assertEquals('SomeAggregate', $message->getAggregateType());
        self::assertEquals(1234, $message->getAggregateId());
        self::assertEquals(5, $message->getSequenceNumber());
        self::assertSame($event, $message->getPayload());
    }

    public function testReconstructUsingExistingData(): void
    {
        $id = Uuid::uuid4();
        $timestamp = DateTime::microsecondsNow();
        $metadata = Metadata::from(['foo' => 'bar']);

        $message = new GenericDomainEventMessage(
            'SomeAggregate',
            1234,
            5,
            new SomePayload(),
            $metadata,
            $id,
            $timestamp
        );

        self::assertSame($metadata, $message->getMetadata());
        self::assertSame($id, $message->getId());
        self::assertSame($timestamp, $message->getTimestamp());
    }
}
