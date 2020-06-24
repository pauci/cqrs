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

        $this->assertSame($payload, $message->getPayload());
        $this->assertEquals(SomePayload::class, $message->getPayloadType());

        $this->assertInstanceOf(Uuid::class, $message->getId());
        $this->assertEquals(4, $message->getId()->getVersion());
        $this->assertEquals([], $message->getMetadata()->toArray());
    }

    public function testReconstructUsingExistingData(): void
    {
        $metadata = Metadata::from(['foo' => 'bar']);
        $uuid = Uuid::uuid4();

        $message = new GenericMessage(new SomePayload(), $metadata, $uuid);

        $this->assertSame($uuid, $message->getId());
        $this->assertSame($metadata, $message->getMetadata());
    }
}