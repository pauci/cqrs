<?php

namespace CQRS\Commanding;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ServiceCommandHandlerLocator implements
    CommandHandlerLocator,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /** @var array */
    private $commandHandlersMap = [];

    /**
     * @param array $commandHandlersMap
     */
    public function __construct(array $commandHandlersMap = [])
    {
        $this->commandHandlersMap = $commandHandlersMap;
    }

    /**
     * Maps given command type to the service of given name
     *
     * @param string $commandType
     * @param string $serviceName
     */
    public function map($commandType, $serviceName)
    {
        $this->commandHandlersMap[strtolower($commandType)] = $serviceName;
    }

    /**
     * @param object $command
     * @return object
     */
    public function getCommandHandler($command)
    {
        $commandType = get_class($command);

        $serviceName = $this->getServiceName($commandType);

        return $this->serviceLocator->get($serviceName);
    }

    /**
     * @param string $commandType
     * @return string
     * @throws \RuntimeException
     */
    private function getServiceName($commandType)
    {
        if (!isset($this->commandHandlersMap[strtolower($commandType)])) {
            throw new \RuntimeException(sprintf(
                "No service mapped for command type '%s'",
                $commandType
            ));
        }

        return $this->commandHandlersMap[strtolower($commandType)];
    }
}
