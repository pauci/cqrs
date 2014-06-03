<?php

namespace CQRS\Options;

use Zend\Stdlib\AbstractOptions;

class CommandHandlerLocator extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\Commanding\ServiceCommandHandlerLocator';

    /** @var array */
    protected $map = [];

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
     * @param array $map
     * @return self
     */
    public function setMap(array $map)
    {
        $this->map = $map;
        return $this;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }
} 
