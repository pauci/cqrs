<?php

namespace CQRSTest\Plugin\Doctrine\Mock;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class EntityManagerMock implements EntityManagerInterface
{
    public $begin = false;
    public $commit = false;
    public $rollback = false;
    public $flush = false;
    public $close = false;

    public function getCache()
    {}

    public function getConnection()
    {}

    public function getExpressionBuilder()
    {}

    public function beginTransaction()
    {
        $this->begin = true;
    }

    public function transactional($func)
    {}

    public function commit()
    {
        $this->commit = true;
    }

    public function rollback()
    {
        $this->rollback = true;
    }

    public function createQuery($dql = '')
    {}

    public function createNamedQuery($name)
    {}

    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {}

    public function createNamedNativeQuery($name)
    {}

    public function createQueryBuilder()
    {}

    public function getReference($entityName, $id)
    {}

    public function getPartialReference($entityName, $identifier)
    {}

    public function close()
    {
        $this->close = true;
    }

    public function copy($entity, $deep = false)
    {}

    public function lock($entity, $lockMode, $lockVersion = null)
    {}

    public function getEventManager()
    {}

    public function getConfiguration()
    {}

    public function isOpen()
    {}

    public function getUnitOfWork()
    {}

    public function getHydrator($hydrationMode)
    {}

    public function newHydrator($hydrationMode)
    {}

    public function getProxyFactory()
    {}

    public function getFilters()
    {}

    public function isFiltersStateClean()
    {}

    public function hasFilters()
    {}

    public function find($className, $id)
    {}

    public function persist($object)
    {}

    public function remove($object)
    {}

    public function merge($object)
    {}

    public function clear($objectName = null)
    {}

    public function detach($object)
    {}

    public function refresh($object)
    {}

    public function flush()
    {
        $this->flush = true;
    }

    public function getRepository($className)
    {}

    public function getClassMetadata($className)
    {}

    public function getMetadataFactory()
    {}

    public function initializeObject($obj)
    {}

    public function contains($object)
    {}
}
