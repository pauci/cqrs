<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Payload;

use CQRS\Domain\Payload\AbstractPayload;

/**
 * @property-read string $protectedFoo
 */
class TestableAbstractPayload extends AbstractPayload
{
    public string $foo;

    protected string $protectedFoo;

    private string $privateFoo;
}
