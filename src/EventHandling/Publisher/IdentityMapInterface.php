<?php

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;

interface IdentityMapInterface
{
    /**
     * @return AggregateRootInterface[]
     */
    public function all();
}
