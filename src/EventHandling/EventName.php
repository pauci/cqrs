<?php

namespace CQRS\EventHandling;

class EventName
{
    /** @var DomainEvent */
    private $event;
    /** @var string */
    private $name;

    /**
     * @param DomainEvent $event
     */
    public function __construct(DomainEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->name === null) {
            $this->name = $this->parseName();
        }
        return $this->name;
    }

    /**
     * @return string
     */
    private function parseName()
    {
        $name = get_class($this->event);

        $pos = strrpos($name, '\\');
        if ($pos !== false) {
            $name = substr($name, $pos + 1);
        }

        if (substr($name, -5) == 'Event') {
            $name = substr($name, 0, -5);
        }

        return $name;
    }
} 
