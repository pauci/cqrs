<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class EventBus extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\EventHandling\SynchronousEventBus';

    /** @var string */
    protected $eventHandlerLocator = 'cqrs_default';

    /** @var string */
    protected $eventStore = 'cqrs_default';

    /**
     * @param string $class
     * @return self
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $eventHandlerLocator
     * @return self
     */
    public function setEventHandlerLocator($eventHandlerLocator)
    {
        $this->eventHandlerLocator = $eventHandlerLocator;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventHandlerLocator()
    {
        return "cqrs.eventHandlerLocator.{$this->eventHandlerLocator}";
    }

    /**
     * @param string $eventStore
     * @return self
     */
    public function setEventStore($eventStore)
    {
        $this->eventStore = $eventStore;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventStore()
    {
        return "cqrs.eventStore.{$this->eventStore}";
    }
}
