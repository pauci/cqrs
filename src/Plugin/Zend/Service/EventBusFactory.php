<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\Plugin\Zend\Options\EventBus;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventBusFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\EventHandling\EventBus
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EventBus $options */
        $options = $this->getOptions($serviceLocator, 'commandBus');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Plugin\Zend\Options\CommandBus';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param CommandBus $options
     * @return \CQRS\CommandHandling\CommandBus
     */
    protected function create(ServiceLocatorInterface $sl, EventBus $options)
    {
        $class = $options->getClass();

        /** @var \CQRS\CommandHandling\CommandHandlerLocator $commandHandlerLocator */
        $commandHandlerLocator = $sl->get($options->getCommandHandlerLocator());

        /** @var \CQRS\CommandHandling\TransactionManager $transactionManager */
        $transactionManager = $sl->get($options->getTransactionManager());

        return new $class($commandHandlerLocator, $transactionManager);
    }
} 
