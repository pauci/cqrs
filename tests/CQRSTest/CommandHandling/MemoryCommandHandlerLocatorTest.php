<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Command;
use CQRS\CommandHandling\MemoryCommandHandlerLocator;

class MemoryCommandHandlerLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testItThrowsExceptionWhenRegisteredServiceIsNoObject()
    {
        $this->setExpectedException('CQRS\Exception\RuntimeException', "No valid service given for command type 'foo'");

        $locator = new MemoryCommandHandlerLocator();
        $locator->register('foo', 'not an object');
    }

    public function testItThrowsExceptionWhenNoHandlerIsRegisteredForCommand()
    {
        $this->setExpectedException('CQRS\Exception\RuntimeException', "No service registered for command type 'CQRSTest\\CommandHandling\\NoHandlerCommand'");

        $locator = new MemoryCommandHandlerLocator();
        $locator->getCommandHandler(new NoHandlerCommand());
    }
}

class NoHandlerCommand implements Command
{}
