<?php

declare(strict_types=1);

namespace CQRSTest\Serializer;

class TestEventWithCustomConstructor
{
    public function __construct(SomeAggregate $someAggregate)
    {
    }
}
