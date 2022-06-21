<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Domain\Message\Metadata;
use DateTimeImmutable;
use Pauci\DateTime\DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GenericEventMessageTest extends TestCase
{
    public function testCreateFromEvent(): void
    {
        $event = new SomePayload();
        $eventMessage = new GenericEventMessage($event);

        self::assertSame($event, $eventMessage->getPayload());
        self::assertInstanceOf(DateTimeImmutable::class, $eventMessage->getTimestamp());
    }

    public function testReconstructUsingExistingData(): void
    {
        $metadata = Metadata::from(['foo' => 'bar']);
        $id = Uuid::uuid4();
        $timestamp = DateTime::now();

        $eventMessage = new GenericEventMessage(new SomePayload(), $metadata, $id, $timestamp);

        self::assertSame($timestamp, $eventMessage->getTimestamp());
        self::assertSame($id, $eventMessage->getId());
        self::assertSame($metadata, $eventMessage->getMetadata());
    }
}
