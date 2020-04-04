<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Message;

use CQRS\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class AbstractCommandTest extends TestCase
{
    public function testCreateThrowsExceptionWhenUnknownPropertySet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "bar" is not a valid property on command "TestAbstract"');

        new TestAbstractCommand(['bar' => 'baz']);
    }

    public function testAccessingUndefinedPropertyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "bar" is not a valid property on command "TestAbstract"');

        $command = new TestAbstractCommand();
        $command->bar;
    }
}
