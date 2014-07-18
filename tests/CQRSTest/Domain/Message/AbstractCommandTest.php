<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\AbstractCommand;
use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class AbstractCommandTest extends PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToPublicProperties()
    {
        $command = new TestAbstractCommand([
            'foo'          => 'bar',
            'protectedFoo' => 'baz'
        ]);

        $this->assertEquals('bar', $command->foo);
        $this->assertEquals('baz', $command->protectedFoo);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'Property "bar" is not a valid property on command "TestAbstract"'
        );

        new TestAbstractCommand(['bar' => 'baz']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $command = new TestAbstractCommand();

        $this->setExpectedException(
            RuntimeException::class,
            'Property "bar" is not a valid property on command "TestAbstract"'
        );

        $command->bar;
    }
}

/**
 * @property-read string $protectedFoo
 */
class TestAbstractCommand extends AbstractCommand
{
    public $foo;
    protected $protectedFoo;
}
