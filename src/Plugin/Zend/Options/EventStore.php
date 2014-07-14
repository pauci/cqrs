<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class EventStore extends AbstractOptions
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $dbalConnection = 'orm_default';

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
     * @param string $dbalConnection
     * @return self
     */
    public function setDbalConnection($dbalConnection)
    {
        $this->dbalConnection = $dbalConnection;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbalConnection()
    {
        return "doctrine.connection.{$this->dbalConnection}";
    }
}
