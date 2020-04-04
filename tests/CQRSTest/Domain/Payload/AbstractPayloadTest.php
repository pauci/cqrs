<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Payload;

use CQRS\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class AbstractPayloadTest extends TestCase
{
    public function testCreateArrayMapsToProperties(): void
    {
        $payload = new TestableAbstractPayload([
            'foo' => 'bar',
            'protectedFoo' => 'baz'
        ]);

        $this->assertEquals('bar', $payload->foo);
        $this->assertEquals('baz', $payload->protectedFoo);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Property "baz" is not a valid property on "CQRSTest\Domain\Payload\TestableAbstractPayload"'
        );

        new TestableAbstractPayload(['baz' => 'value']);
    }

    public function testCreateThrowsExceptionWhenPrivatePropertySet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Property "privateFoo" is not a valid property on "CQRSTest\Domain\Payload\TestableAbstractPayload"'
        );

        new TestableAbstractPayload(['privateFoo' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Property "baz" is not a valid property on "CQRSTest\Domain\Payload\TestableAbstractPayload"'
        );

        $message = new TestableAbstractPayload(['foo' => 'bar']);
        $message->baz;
    }
}