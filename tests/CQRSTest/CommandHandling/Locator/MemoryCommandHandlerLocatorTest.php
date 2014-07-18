<?php

namespace CQRSTest\CommandHandling\Locator;

use CQRS\CommandHandling\CommandInterface;
use CQRS\CommandHandling\Locator\MemoryCommandHandlerLocator;
use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;
use stdClass;

class MemoryCommandHandlerLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testRegisterAndGetCommandHandler()
    {
        $handler = new stdClass();

        $locator = new MemoryCommandHandlerLocator();
        $locator->register(HandleCommand::class, $handler);

        $this->assertSame($handler, $locator->getCommandHandler(new HandleCommand()));
    }

    public function testItThrowsExceptionWhenRegisteredServiceIsNoObject()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'No valid service given for command type "foo"; expected object, got string'
        );

        $locator = new MemoryCommandHandlerLocator();
        $locator->register('foo', 'not an object');
    }

    public function testItThrowsExceptionWhenNoHandlerIsRegisteredForCommand()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'No service registered for command type "CQRSTest\CommandHandling\Locator\NoHandlerCommand"'
        );

        $locator = new MemoryCommandHandlerLocator();
        $locator->getCommandHandler(new NoHandlerCommand());
    }
}

class HandleCommand implements CommandInterface
{}

class NoHandlerCommand implements CommandInterface
{}
