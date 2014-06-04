<?php

namespace CQRS\Plugin\Zend\EventHandling;

use CQRS\EventHandling\EventHandlerLocator;
use CQRS\EventHandling\EventName;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ServiceEventHandlerLocator implements
    EventHandlerLocator,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /** @var array */
    private $handlersMap = [];

    /**
     * @param EventName $eventName
     * @return array
     */
    public function getEventHandlers(EventName $eventName)
    {
        $eventName = strtolower($eventName);

        if (!isset($this->handlersMap[$eventName])) {
            return [];
        }

        $eventHandlers = [];
        foreach ($this->handlersMap[$eventName] as $serviceName) {
            $eventHandlers[] = $this->serviceLocator->get($serviceName);
        }

        return $eventHandlers;
    }

    /**
     * Maps given event to the service(s) of given name(s)
     *
     * @param string $eventName
     * @param array|string $serviceNames
     */
    public function register($eventName, $serviceNames)
    {
        $eventName = strtolower($eventName);

        if (!is_array($serviceNames)) {
            $serviceNames = [$serviceNames];
        }

        if (!isset($this->handlersMap[$eventName])) {
            $this->handlersMap[$eventName] = [];
        }

        foreach ($serviceNames as $serviceName) {
            $this->handlersMap[$eventName][] = $serviceName;
        }
    }
}
