<?php

namespace CQRS\Service;

use CQRS\Plugin\Doctrine\CommandHandling\AbstractTransactionManager;
use CQRS\Plugin\Zend\Options\TransactionManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class TransactionManagerFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\CommandHandling\TransactionManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var TransactionManager $options */
        $options = $this->getOptions($serviceLocator, 'commandBus');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Plugin\Zend\Options\TransactionManager';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param TransactionManager $options
     * @return \CQRS\CommandHandling\TransactionManager
     */
    protected function create(ServiceLocatorInterface $sl, TransactionManager $options)
    {
        $class = $options->getClass();

        $transactionManager = new $class;

        if ($transactionManager instanceof AbstractTransactionManager) {
            /** @var \Doctrine\ORM\EntityManager $entityManager */
            $entityManager = $sl->get($options->getEntityManager());
            $transactionManager->setEntityManager($entityManager);
        }

        return $transactionManager;
    }
} 
