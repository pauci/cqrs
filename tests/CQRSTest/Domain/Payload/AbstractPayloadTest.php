<?php
namespace CQRSTest\Domain\Payload;

use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class AbstractPayloadTest extends PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToProperties()
    {
        $payload = new TestableAbstractPayload([
            'foo'          => 'bar',
            'protectedFoo' => 'baz'
        ]);

        $this->assertEquals('bar', $payload->foo);
        $this->assertEquals('baz', $payload->protectedFoo);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'Property "baz" is not a valid property on "CQRSTest\Domain\Payload\TestableAbstractPayload"'
        );

        new TestableAbstractPayload(['baz' => 'value']);
    }

    public function testCreateThrowsExceptionWhenPrivatePropertySet()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'Property "privateFoo" is not a valid property on "CQRSTest\Domain\Payload\TestableAbstractPayload"'
        );

        new TestableAbstractPayload(['privateFoo' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $message = new TestableAbstractPayload(['foo' => 'bar']);

        $this->setExpectedException(
            RuntimeException::class,
            'Property "baz" is not a valid property on "CQRSTest\Domain\Payload\TestableAbstractPayload"'
        );

        $message->baz;
    }
}