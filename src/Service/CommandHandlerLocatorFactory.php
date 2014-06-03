<?php

namespace CQRS\Service;

use CQRS\Options\CommandHandlerLocator;
use CQRS\Commanding\ServiceCommandHandlerLocator;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommandHandlerLocatorFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\Commanding\CommandHandlerLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $options \CQRS\Options\CommandHandlerLocator */
        $options = $this->getOptions($serviceLocator, 'commandHandlerLocator');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Options\CommandHandlerLocator';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param CommandHandlerLocator $options
     * @return \CQRS\Commanding\CommandHandlerLocator
     * @throws RuntimeException
     */
    protected function create(ServiceLocatorInterface $sl, CommandHandlerLocator $options)
    {
        $class = $options->getClass();

        if (!$class) {
            throw new RuntimeException('CommandHandlerLocator must have a class name to instantiate');
        }

        $commandHandlerLocator = new $class;

        if ($commandHandlerLocator instanceof ServiceLocatorAwareInterface) {
            $commandHandlerLocator->setServiceLocator($sl);
        }

        if ($commandHandlerLocator instanceof ServiceCommandHandlerLocator) {
            $commandHandlersMap = $options->getMap();

            foreach ($commandHandlersMap as $serviceName => $commandTypes) {
                foreach ($commandTypes as $commandType) {
                    $commandHandlerLocator->map($commandType, $serviceName);
                }
            }
        }

        return $commandHandlerLocator;
    }
}
