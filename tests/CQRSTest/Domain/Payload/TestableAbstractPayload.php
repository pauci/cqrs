<?php

namespace CQRSTest\Domain\Payload;

use CQRS\Domain\Payload\AbstractPayload;

/**
 * @property-read string $protectedFoo
 */
class TestableAbstractPayload extends AbstractPayload
{
    public $foo;

    protected $protectedFoo;

    private $privateFoo;
}
