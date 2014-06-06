<?php

namespace CQRS\Plugin\Zend\Options;

use Zend\Stdlib\AbstractOptions;

class TransactionManager extends AbstractOptions
{
    /** @var string */
    protected $class = 'CQRS\CommandHandling\NoTransactionManager';

    /** @var string */
    protected $ormEntityManager = 'orm_default';

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
     * @param string $ormEntityManager
     * @return self
     */
    public function setOrmEntityManager($ormEntityManager)
    {
        $this->ormEntityManager = $ormEntityManager;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrmEntityManager()
    {
        return "doctrine.entitymanager.{$this->ormEntityManager}";
    }
} 
