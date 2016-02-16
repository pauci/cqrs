<?php

namespace CQRS\EventStream;

use Ramsey\Uuid\UuidInterface;

abstract class AbstractNestedEventStream implements EventStreamInterface
{
    /**
     * @var EventStreamInterface
     */
    protected $eventStream;

    /**
     * @param EventStreamInterface $eventStream
     */
    public function __construct(EventStreamInterface $eventStream)
    {
        $this->eventStream = $eventStream;
    }

    /**
     * @return UuidInterface|null
     */
    public function getLastEventId()
    {
        return $this->eventStream->getLastEventId();
    }
}
