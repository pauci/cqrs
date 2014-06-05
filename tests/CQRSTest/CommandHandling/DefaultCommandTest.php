<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\DefaultCommand;

class DefaultCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToPublicProperties()
    {
        $command = new TestCommand([
            'test'          => 'value',
            'protectedTest' => 'protectedValue'
        ]);

        $this->assertEquals('value', $command->test);
        $this->assertEquals('protectedValue', $command->protectedTest);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Property "unknown" is not a valid property on command "Test".'
        );

        new TestCommand(['unknown' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $command = new TestCommand();

        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Property "unknown" is not a valid property on command "Test".'
        );

        $value = $command->unknown;
    }
}

/**
 * @property-read string $protectedTest
 */
class TestCommand extends DefaultCommand
{
    public $test;
    protected $protectedTest;
}
