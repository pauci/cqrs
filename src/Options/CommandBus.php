<?php

namespace CQRS\Options;

use Zend\Stdlib\AbstractOptions;

class CommandBus extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\Commanding\SequentialCommandBus';

    /** @var string */
    protected $commandHandlerLocator = 'cqrs_default';

    /** @var string */
    protected $entityManager = 'orm_default';

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
     * @param string $commandHandlerLocator
     * @return self
     */
    public function setCommandHandlerLocator($commandHandlerLocator)
    {
        $this->commandHandlerLocator = $commandHandlerLocator;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommandHandlerLocator()
    {
        return "cqrs.commandHandlerLocator.{$this->commandHandlerLocator}";
    }

    /**
     * @param string $entityManager
     * @return self
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityManager()
    {
        return "doctrine.entitymanager.{$this->entityManager}";
    }
} 
