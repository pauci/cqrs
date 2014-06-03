<?php

namespace CQRS\Service;

use CQRS\Commanding\OrmTransactionalCommandBusWrapper;
use CQRS\Options\CommandBus;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommandBusFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return OrmTransactionalCommandBusWrapper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \CQRS\Options\CommandBus $options */
        $options = $this->getOptions($serviceLocator, 'commandBus');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Options\CommandBus';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param CommandBus $options
     * @return OrmTransactionalCommandBusWrapper
     */
    protected function create(ServiceLocatorInterface $sl, CommandBus $options)
    {
        $class = $options->getClass();

        /** @var \CQRS\Commanding\CommandHandlerLocator $commandHandlerLocator */
        $commandHandlerLocator = $sl->get($options->getCommandHandlerLocator());

        $commandBus = new $class($commandHandlerLocator);

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $sl->get($options->getEntityManager());
        return new OrmTransactionalCommandBusWrapper($entityManager, $commandBus);
    }
}
