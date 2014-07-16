<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\AbstractEvent;
use CQRS\Domain\Message\EventInterface;
use Exception;

/**
 * @property-read Exception $exception
 * @property-read EventInterface $event
 */
class EventExecutionFailed extends AbstractEvent
{
    /** @var Exception */
    protected $exception;

    /** @var EventInterface */
    protected $event;
}
