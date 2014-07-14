<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\DomainEventMessageInterface;
use Rhumsaa\Uuid\Uuid;

interface AggregateRootInterface
{
    /**
     * @return Uuid|int
     */
    public function getId();

    /**
     * @return DomainEventMessageInterface[]
     */
    public function pullDomainEvents();

    /**
     * @return bool
     */
    public function isDeleted();
} 
