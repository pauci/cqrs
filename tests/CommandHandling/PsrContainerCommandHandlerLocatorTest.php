<?php

declare(strict_types=1);

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Exception\CommandHandlerNotFoundException;
use CQRS\CommandHandling\PsrContainerCommandHandlerLocator;
use CQRSTest\Stubs\DummyCallableContainer;
use PHPUnit\Framework\TestCase;

class PsrContainerCommandHandlerLocatorTest extends TestCase
{
    public function testRegisterAndGetCommandHandler(): void
    {
        $locator = new PsrContainerCommandHandlerLocator(new DummyCallableContainer(), ['Command' => 'service']);

        $handler = $locator->get('Command');

        self::assertEquals('service', $handler());
    }

    public function testItThrowsExceptionWhenNoHandlerIsRegisteredForCommand(): void
    {
        $this->expectException(CommandHandlerNotFoundException::class);
        $this->expectExceptionMessage('Command handler for CommandWithoutHandler not found');

        $locator = new PsrContainerCommandHandlerLocator(new DummyCallableContainer(), []);
        $locator->get('CommandWithoutHandler');
    }
}
