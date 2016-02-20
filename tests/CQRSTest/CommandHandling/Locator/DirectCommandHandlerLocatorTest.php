<?php

namespace CQRSTest\CommandHandling\Locator;

use CQRS\CommandHandling\Locator\CommandHandlerNotFoundException;
use CQRS\CommandHandling\Locator\DirectCommandHandlerLocator;
use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class DirectCommandHandlerLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testRegisterAndGetCommandHandler()
    {
        $handler = function () {};

        $locator = new DirectCommandHandlerLocator();
        $locator->add('Command', $handler);

        $this->assertSame($handler, $locator->get('Command'));
    }

    public function testItThrowsExceptionIfHandlerIsNotCallable()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'No valid command handler given for foo; expected callable, got string'
        );

        $locator = new DirectCommandHandlerLocator();
        $locator->add('foo', 'not an object');
    }

    public function testItThrowsExceptionWhenNoHandlerIsRegisteredForCommand()
    {
        $this->setExpectedException(
            CommandHandlerNotFoundException::class,
            'Command handler for CommandWithoutHandler not found'
        );

        $locator = new DirectCommandHandlerLocator();
        $locator->get('CommandWithoutHandler');
    }
}
