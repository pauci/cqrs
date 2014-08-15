<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\DomainEventMessageInterface;

interface AggregateRootInterface
{
    /**
     * @return mixed
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
