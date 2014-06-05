<?php

namespace CQRS\EventHandling;

/**
 * @property-read string $service
 * @property-read \Exception $exception
 * @property-read DomainEvent $event
 */
class EventExecutionFailed extends DefaultDomainEvent
{
    /** @var string */
    protected $service;
    /** @var \Exception */
    protected $exception;
    /** @var DomainEvent */
    protected $event;
}
