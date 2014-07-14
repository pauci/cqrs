<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\AbstractEventMessage;
use CQRS\Domain\Message\EventMessageInterface;
use Exception;

/**
 * @property-read Exception $exception
 * @property-read EventMessageInterface $event
 */
class EventExecutionFailed extends AbstractEventMessage
{
    /** @var Exception */
    protected $exception;

    /** @var EventMessageInterface */
    protected $event;
}
