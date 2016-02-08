<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;
use Exception;
use JsonSerializable;

class EventExecutionFailed implements JsonSerializable
{
    /**
     * @var EventMessageInterface
     */
    protected $event;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @param EventMessageInterface $event
     * @param Exception $exception
     */
    public function __construct(EventMessageInterface $event, Exception $exception)
    {
        $this->event = $event;
        $this->exception = $exception;
    }

    /**
     * @return EventMessageInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'event' => $this->event,
            'exception' => $this->exception,
        ];
    }
}
