<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\DefaultCommand;

class DefaultCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToPublicProperties()
    {
        $command = new TestCommand(['test' => 'value']);

        $this->assertEquals('value', $command->test);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException('RuntimeException', 'Property "unknown" is not a valid property on command "Test".');

        new TestCommand(['unknown' => 'value']);
    }
}

class TestCommand extends DefaultCommand
{
    public $test;
}
