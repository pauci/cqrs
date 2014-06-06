<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class EventStore extends AbstractOptions
{
    /** @var string */
    protected $class = '';

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
} 
