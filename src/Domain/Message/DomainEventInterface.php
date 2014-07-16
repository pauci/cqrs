<?php

namespace CQRS\Domain\Message;

interface DomainEventInterface extends EventInterface
{
    public function getAggregateType();

    public function getAggregateId();
}
