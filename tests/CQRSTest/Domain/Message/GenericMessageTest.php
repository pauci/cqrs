<?php
namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericMessage;
use PHPUnit_Framework_TestCase;
use Rhumsaa\Uuid\Uuid;

class GenericMessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFromPayload()
    {
        $payload = new SomePayload();
        $message = new GenericMessage($payload);

        $this->assertSame($payload, $message->getPayload());
        $this->assertEquals(SomePayload::class, $message->getPayloadType());

        $this->assertInstanceOf(Uuid::class, $message->getId());
        $this->assertEquals(4, $message->getId()->getVersion());
        $this->assertEquals([], $message->getMetadata()->toArray());
    }

    public function testReconstructUsingExistingData()
    {
        $metadata = ['foo' => 'bar'];
        $uuid     = Uuid::uuid4();

        $message = new GenericMessage(new SomePayload(), $metadata, $uuid);

        $this->assertSame($uuid, $message->getId());
        $this->assertEquals($metadata, $message->getMetadata()->toArray());
    }
}

class SomePayload
{}
