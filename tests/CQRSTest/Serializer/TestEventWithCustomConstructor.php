<?php

namespace CQRSTest\Serializer;


class TestEventWithCustomConstructor
{
    public function __construct(SomeAggregate $someAggregate)
    {
        
    }
}

