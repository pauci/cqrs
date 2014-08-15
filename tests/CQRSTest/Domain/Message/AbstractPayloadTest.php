<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Payload\AbstractPayload;
use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class AbstractPayloadTest extends PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToProperties()
    {
        $payload = new Payload([
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
            'Property "baz" is not a valid property on "CQRSTest\Domain\Message\Payload"'
        );

        new Payload(['baz' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $message = new Payload(['foo' => 'bar']);

        $this->setExpectedException(
            RuntimeException::class,
            'Property "baz" is not a valid property on "CQRSTest\Domain\Message\Payload"'
        );

        $message->baz;
    }
}

/**
 * @property-read string $protectedFoo
 */
class Payload extends AbstractPayload
{
    public $foo;
    protected $protectedFoo;
}
