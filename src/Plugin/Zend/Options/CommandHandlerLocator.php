<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class CommandHandlerLocator extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\Plugin\Zend\CommandHandling\ServiceCommandHandlerLocator';

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
