<?php

declare(strict_types=1);

namespace CQRSTest\Serializer;

use CQRS\Domain\Payload\AbstractEvent;
use Pauci\DateTime\DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use stdClass;

class SomeEvent extends AbstractEvent
{
    protected string $foo;
    protected UuidInterface $id;
    protected DateTimeInterface $time;
    protected stdClass $object;
}

