<?php

declare(strict_types=1);

namespace CQRSTest\Serializer;

use Pauci\DateTime\DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use stdClass;

class SomeEvent
{
    protected string $foo;
    protected UuidInterface $id;
    protected DateTimeInterface $time;
    protected stdClass $object;
}

