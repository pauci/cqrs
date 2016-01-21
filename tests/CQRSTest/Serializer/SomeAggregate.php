<?php

namespace CQRSTest\Serializer;

use CQRS\Domain\Model\AbstractAggregateRoot;

class SomeAggregate extends AbstractAggregateRoot
{
    public function getId()
    {
        return 4;
    }
}
