<?php

namespace CQRSTest\Domain\Message;

use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class AbstractCommandTest extends PHPUnit_Framework_TestCase
{
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
