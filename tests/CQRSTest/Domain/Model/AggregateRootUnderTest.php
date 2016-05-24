<?php

namespace CQRSTest\Domain\Model;

use CQRS\Domain\Model\AbstractAggregateRoot;

class AggregateRootUnderTest extends AbstractAggregateRoot
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function raise($event)
    {
        $this->registerEvent($event);
    }
}

