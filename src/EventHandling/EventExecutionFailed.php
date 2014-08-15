<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Payload\AbstractEvent;
use CQRS\Domain\Message\EventMessageInterface;
use Exception;

/**
 * @property-read Exception $exception
 * @property-read EventMessageInterface $event
 */
class EventExecutionFailed extends AbstractEvent
{
    /** @var Exception */
    protected $exception;

    /** @var EventMessageInterface */
    protected $event;
}
