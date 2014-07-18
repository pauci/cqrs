<?php
namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\AbstractMessage;
use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class AbstractMessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToProperties()
    {
        $message = new AbstractMessageUnderTest([
            'foo'          => 'bar',
            'protectedFoo' => 'baz'
        ]);

        $this->assertEquals('bar', $message->foo);
        $this->assertEquals('baz', $message->protectedFoo);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'Property "baz" is not a valid property on message "CQRSTest\Domain\Message\AbstractMessageUnderTest"'
        );

        new AbstractMessageUnderTest(['baz' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $message = new AbstractMessageUnderTest(['foo' => 'bar']);

        $this->setExpectedException(
            RuntimeException::class,
            'Property "baz" is not a valid property on message "CQRSTest\Domain\Message\AbstractMessageUnderTest"'
        );

        $message->baz;
    }
}

/**
 * @property-read string $protectedTest
 */
class AbstractMessageUnderTest extends AbstractMessage
{
    public $foo;
    protected $protectedFoo;
}
