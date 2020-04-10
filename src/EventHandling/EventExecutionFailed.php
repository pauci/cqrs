<?php

declare(strict_types=1);

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;
use Exception;
use JsonSerializable;

class EventExecutionFailed implements JsonSerializable
{
    protected EventMessageInterface $event;

    protected Exception $exception;

    public function __construct(EventMessageInterface $event, Exception $exception)
    {
        $this->event = $event;
        $this->exception = $exception;
    }

    public function getEvent(): EventMessageInterface
    {
        return $this->event;
    }

    public function getException(): Exception
    {
        return $this->exception;
    }

    public function jsonSerialize(): array
    {
        return [
            'event' => $this->event,
            'exception' => $this->exception,
        ];
    }
}
