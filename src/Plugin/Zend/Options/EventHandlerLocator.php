<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class EventHandlerLocator extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\EventHandling\MemoryEventHandlerLocator';

    /** @var array */
    protected $handlers = [];

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
     * @param array $handlers
     * @return self
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = $handlers;
        return $this;
    }

    /**
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
