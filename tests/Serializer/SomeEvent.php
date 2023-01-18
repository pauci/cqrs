<?php

declare(strict_types=1);

namespace CQRSTest\Serializer;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use stdClass;

class SomeEvent
{
    protected string $foo;
    protected UuidInterface $id;
    protected DateTimeImmutable $time;
    protected stdClass $object;
}
