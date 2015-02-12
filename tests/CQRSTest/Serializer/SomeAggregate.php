<?php

namespace CQRSTest\Serializer;

use CQRS\Domain\Model\AbstractAggregateRoot;

class SomeAggregate extends AbstractAggregateRoot
{
    public function getId()
    {
        return $this->getIdReference();
    }

    protected function &getIdReference()
    {
        $id = 4;
        return $id;
    }
}
