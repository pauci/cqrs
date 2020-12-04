<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericMessage;
use CQRS\Domain\Message\Metadata;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GenericMessageTest extends TestCase
{
    public function testCreateFromPayload(): void
    {
        $payload = new SomePayload();
        $message = new GenericMessage($payload);

        self::assertSame($payload, $message->getPayload());
        self::assertEquals(SomePayload::class, $message->getPayloadType());

        self::assertInstanceOf(Uuid::class, $message->getId());
        self::assertEquals(4, $message->getId()->getVersion());
        self::assertEquals([], $message->getMetadata()->toArray());
    }

    public function testReconstructUsingExistingData(): void
    {
        $metadata = Metadata::from(['foo' => 'bar']);
        $uuid = Uuid::uuid4();

        $message = new GenericMessage(new SomePayload(), $metadata, $uuid);

        self::assertSame($uuid, $message->getId());
        self::assertSame($metadata, $message->getMetadata());
    }
}
