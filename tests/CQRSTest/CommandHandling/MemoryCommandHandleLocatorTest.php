<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Command;
use CQRS\CommandHandling\MemoryCommandHandlerLocator;
use stdClass;

class MemoryCommandHandlerLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterAndGetCommandHandler()
    {
        $handler = new stdClass();

        $locator = new MemoryCommandHandlerLocator();
        $locator->register('CQRSTest\CommandHandling\HandleCommand', $handler);

        $this->assertSame($handler, $locator->getCommandHandler(new HandleCommand()));
    }

    public function testItThrowsExceptionWhenRegisteredServiceIsNoObject()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'No valid service given for command type "foo"; expected object, got string'
        );

        $locator = new MemoryCommandHandlerLocator();
        $locator->register('foo', 'not an object');
    }

    public function testItThrowsExceptionWhenNoHandlerIsRegisteredForCommand()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'No service registered for command type "CQRSTest\\CommandHandling\\NoHandlerCommand"'
        );

        $locator = new MemoryCommandHandlerLocator();
        $locator->getCommandHandler(new NoHandlerCommand());
    }
}

class HandleCommand implements Command
{}

class NoHandlerCommand implements Command
{}
