<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\Plugin\Zend\Options\EventBus as EventBusOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventBusFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\EventHandling\EventBusInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EventBusOptions $options */
        $options = $this->getOptions($serviceLocator, 'eventBus');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Plugin\Zend\Options\EventBus';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param EventBusOptions $options
     * @return \CQRS\EventHandling\EventBusInterface
     */
    protected function create(ServiceLocatorInterface $sl, EventBusOptions $options)
    {
        $class = $options->getClass();

        /** @var \CQRS\EventHandling\Locator\EventHandlerLocatorInterface $eventHandlerLocator */
        $eventHandlerLocator = $sl->get($options->getEventHandlerLocator());


        /** @var \CQRS\EventHandling\EventStore $eventStore */
        //$eventStore = $sl->get($options->getEventStore());

        return new $class($eventHandlerLocator/*, $eventStore*/);
    }
} 
