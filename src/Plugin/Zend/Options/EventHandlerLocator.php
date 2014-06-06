<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class EventHandlerLocator extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\Plugin\Zend\EventHandling\ServiceEventHandlerLocator';

    /** @var array */
    protected $callbacks = [];

    /** @var array */
    protected $subscribers = [];

    /** @var array */
    protected $services = [];

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
     * @param array $callbacks
     * @return self
     */
    public function setCallbacks(array $callbacks)
    {
        $this->callbacks = $callbacks;
        return $this;
    }

    /**
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @param array $subscribers
     * @return self
     */
    public function setSubscribers(array $subscribers)
    {
        $this->subscribers = $subscribers;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param array $services
     * @return self
     */
    public function setServices(array $services)
    {
        $this->services = $services;
        return $this;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }
}
