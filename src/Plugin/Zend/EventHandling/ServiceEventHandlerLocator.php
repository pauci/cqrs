<?php

namespace CQRS\Plugin\Zend\EventHandling;

use CQRS\EventHandling\EventName;
use CQRS\EventHandling\MemoryEventHandlerLocator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ServiceEventHandlerLocator extends MemoryEventHandlerLocator implements
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /** @var array */
    private $services = [];

    /** @var array */
    private $subscribedServices = [];

    /**
     * @param EventName $eventName
     * @return array
     */
    public function getEventHandlers(EventName $eventName)
    {
        $this->subscribeServices($eventName);

        return parent::getEventHandlers($eventName);
    }

    /**
     * Maps given event name(s) to the service(s) of given name(s)
     *
     * @param string|array $eventName
     * @param string|array $serviceName
     * @param int $priority
     */
    public function registerService($eventName, $serviceName, $priority = 1)
    {
        $eventNames   = (array) $eventName;
        $serviceNames = (array) $serviceName;

        foreach ($eventNames as $eventName) {
            $eventName = strtolower($eventName);

            foreach ($serviceNames as $serviceName) {
                $this->services[$eventName][$priority][] = $serviceName;
            }
        }
    }

    /**
     * @param EventName $eventName
     */
    private function subscribeServices($eventName)
    {
        $eventName = strtolower($eventName);

        if (!isset($this->services[$eventName])) {
            return;
        }

        foreach ($this->services[$eventName] as $priority => $serviceNames) {
            foreach ($serviceNames as $serviceName) {
                // Prevent multiple subscriptions of same service
                if (array_key_exists($serviceName, $this->subscribedServices)) {
                    continue;
                }

                $service = $this->serviceLocator->get($serviceName);
                $this->registerSubscriber($service, $priority);
                $this->subscribedServices[$serviceName] = true;
            }
        }

        unset($this->services[$eventName]);
    }
}
