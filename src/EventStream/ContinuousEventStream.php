<?php

namespace CQRS\EventStream;

use Generator;
use IteratorAggregate;

class ContinuousEventStream implements IteratorAggregate, EventStreamInterface
{
    /**
     * @var EventStreamInterface
     */
    private $eventStream;

    /**
     * @var int
     */
    private $pauseMicroseconds;

    /**
     * @param EventStreamInterface $eventStream
     * @param int                  $pauseMicroseconds
     */
    public function __construct(EventStreamInterface $eventStream, $pauseMicroseconds = 500000)
    {
        $this->eventStream = $eventStream;
        $this->pauseMicroseconds = $pauseMicroseconds;
    }

    /**
     * @return Generator
     */
    public function getIterator()
    {
        while (true) {
            foreach ($this->eventStream as $event) {
                yield $event;
            }
            usleep($this->pauseMicroseconds);
        }
    }
}
