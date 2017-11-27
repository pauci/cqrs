<?php

namespace CQRSTest\Domain\Message;

use CQRS\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class AbstractCommandTest extends TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Property "bar" is not a valid property on command "TestAbstract"
     */
    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        new TestAbstractCommand(['bar' => 'baz']);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Property "bar" is not a valid property on command "TestAbstract"
     */
    public function testAccessingUndefinedPropertyThrowsException()
    {
        $command = new TestAbstractCommand();
        $command->bar;
    }
}
