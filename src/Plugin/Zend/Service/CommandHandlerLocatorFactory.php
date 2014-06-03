<?php

namespace CQRS\Service;

use CQRS\Plugin\Zend\Options\CommandHandlerLocator;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommandHandlerLocatorFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\CommandHandling\CommandHandlerLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $options CommandHandlerLocator */
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
     * @return \CQRS\CommandHandling\CommandHandlerLocator
     * @throws RuntimeException
     */
    protected function create(ServiceLocatorInterface $sl, CommandHandlerLocator $options)
    {
        $class = $options->getClass();

        if (!$class) {
            throw new RuntimeException('CommandHandlerLocator must have a class name to instantiate');
        }

        /** @var \CQRS\CommandHandling\CommandHandlerLocator $commandHandlerLocator */
        $commandHandlerLocator = new $class;

        if ($commandHandlerLocator instanceof ServiceLocatorAwareInterface) {
            $commandHandlerLocator->setServiceLocator($sl);
        }

        $handlers = $options->getHandlers();

        foreach ($handlers as $commandTypeOrServiceName => $serviceOrCommandTypes) {

            if (is_array($serviceOrCommandTypes)) {
                $commandTypes = $serviceOrCommandTypes;
                $service      = $commandTypeOrServiceName;

                foreach ($commandTypes as $commandType) {
                    $commandHandlerLocator->register($commandType, $service);
                }
            } else {
                $commandType = $commandTypeOrServiceName;
                $service     = $serviceOrCommandTypes;

                $commandHandlerLocator->register($commandType, $service);
            }
        }

        return $commandHandlerLocator;
    }
}
