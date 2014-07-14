<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\Plugin\Zend\Options\CommandHandlerLocator as CommandHandlerLocatorOptions;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommandHandlerLocatorFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\CommandHandling\Locator\CommandHandlerLocatorInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var CommandHandlerLocatorOptions $options */
        $options = $this->getOptions($serviceLocator, 'commandHandlerLocator');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Plugin\Zend\Options\CommandHandlerLocator';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param CommandHandlerLocatorOptions $options
     * @return \CQRS\CommandHandling\Locator\CommandHandlerLocatorInterface
     * @throws RuntimeException
     */
    protected function create(ServiceLocatorInterface $sl, CommandHandlerLocatorOptions $options)
    {
        $class = $options->getClass();

        if (!$class) {
            throw new RuntimeException('CommandHandlerLocatorInterface must have a class name to instantiate');
        }

        /** @var \CQRS\CommandHandling\Locator\CommandHandlerLocatorInterface $commandHandlerLocator */
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
