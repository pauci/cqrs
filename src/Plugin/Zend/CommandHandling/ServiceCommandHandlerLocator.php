<?php

namespace CQRS\Plugin\Zend\CommandHandling;

use CQRS\CommandHandling\CommandInterface;
use CQRS\CommandHandling\Locator\CommandHandlerLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ServiceCommandHandlerLocator implements
    CommandHandlerLocatorInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /** @var array */
    private $handlersMap = [];

    /**
     * @param CommandInterface $command
     * @return object
     */
    public function getCommandHandler(CommandInterface $command)
    {
        $serviceName = $this->getServiceName(get_class($command));

        return $this->serviceLocator->get($serviceName);
    }

    /**
     * Maps given command type to the service of given name
     *
     * @param string $commandType
     * @param string $serviceName
     */
    public function register($commandType, $serviceName)
    {
        $this->handlersMap[strtolower($commandType)] = $serviceName;
    }

    /**
     * @param string $commandType
     * @return string
     * @throws \RuntimeException
     */
    private function getServiceName($commandType)
    {
        $key = strtolower($commandType);
        if (!isset($this->handlersMap[$key])) {
            throw new \RuntimeException(sprintf(
                "No service mapped for command type '%s'",
                $commandType
            ));
        }

        return $this->handlersMap[$key];
    }
}
