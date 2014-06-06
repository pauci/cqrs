<?php

namespace CQRS\EventHandling;

/**
 * @property-read \Exception $exception
 * @property-read DomainEvent $event
 */
class EventExecutionFailed extends DefaultDomainEvent
{
    /** @var \Exception */
    protected $exception;
    /** @var DomainEvent */
    protected $event;
}
