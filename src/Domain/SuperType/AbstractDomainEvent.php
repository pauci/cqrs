<?php

namespace CQRS\Domain\SuperType;

use CQRS\EventHandling\DomainEvent;
use DateTime;

abstract class AbstractDomainEvent implements DomainEvent
{
    /** @var string */
    public $id;
    /** @var string */
    public $aggregateType;
    /** @var int */
    public $aggregateId;
    /** @var DateTime */
    public $occurredAt;
}
