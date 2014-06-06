<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class TransactionManager extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\CommandHandling\NoTransactionManager';

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
