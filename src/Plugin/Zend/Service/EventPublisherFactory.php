<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\Plugin\Zend\Options\EventPublisher as EventPublisherOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventPublisherFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\EventHandling\EventPublisher
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EventPublisherOptions $options */
        $options = $this->getOptions($serviceLocator, 'eventPublisher');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Plugin\Zend\Options\EventPublisher';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param EventPublisherOptions $options
     * @return \CQRS\EventHandling\EventBus
     */
    protected function create(ServiceLocatorInterface $sl, EventPublisherOptions $options)
    {
        $class = $options->getClass();

        /** @var \CQRS\EventHandling\EventBus $eventBus */
        $eventBus = $sl->get($options->getEventBus());

        return new $class($eventBus);
    }
} 
