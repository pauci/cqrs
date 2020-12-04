<?php

declare(strict_types=1);

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Exception\CommandHandlerNotFoundException;
use CQRS\CommandHandling\CommandHandlerLocator;
use PHPUnit\Framework\TestCase;

class CommandHandlerLocatorTest extends TestCase
{
    public function testRegisterAndGetCommandHandler(): void
    {
        $handler = function () {};

        $locator = new CommandHandlerLocator();
        $locator->set('Command', $handler);

        self::assertSame($handler, $locator->get('Command'));
    }

    public function testItThrowsExceptionWhenNoHandlerIsRegisteredForCommand(): void
    {
        $this->expectException(CommandHandlerNotFoundException::class);
        $this->expectExceptionMessage('Command handler for CommandWithoutHandler not found');

        $locator = new CommandHandlerLocator();
        $locator->get('CommandWithoutHandler');
    }
}
