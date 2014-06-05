<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\DefaultCommand;

class DefaultCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToPublicProperties()
    {
        $command = new TestDefaultCommand([
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
            'Property "unknown" is not a valid property on command "TestDefault"'
        );

        new TestDefaultCommand(['unknown' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $command = new TestDefaultCommand();

        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Property "unknown" is not a valid property on command "TestDefault"'
        );

        $value = $command->unknown;
    }
}

/**
 * @property-read string $protectedTest
 */
class TestDefaultCommand extends DefaultCommand
{
    public $test;
    protected $protectedTest;
}
