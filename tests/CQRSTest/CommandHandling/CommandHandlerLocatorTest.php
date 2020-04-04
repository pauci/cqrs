<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Exception\CommandHandlerNotFoundException;
use CQRS\CommandHandling\CommandHandlerLocator;
use CQRS\CommandHandling\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CommandHandlerLocatorTest extends TestCase
{
    public function testRegisterAndGetCommandHandler()
    {
        $handler = function () {};

        $locator = new CommandHandlerLocator();
        $locator->set('Command', $handler);

        $this->assertSame($handler, $locator->get('Command'));
    }

    public function testItThrowsExceptionIfEventTypeIsNotString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Command type must be a string; got integer');

        $locator = new CommandHandlerLocator();
        $locator->set(123, 'handler');
    }

    public function testItThrowsExceptionWhenNoHandlerIsRegisteredForCommand()
    {
        $this->expectException(CommandHandlerNotFoundException::class);
        $this->expectExceptionMessage('Command handler for CommandWithoutHandler not found');

        $locator = new CommandHandlerLocator();
        $locator->get('CommandWithoutHandler');
    }
}
