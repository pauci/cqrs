<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\Plugin\Zend\Options\EventStore as EventStoreOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventStoreFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\EventHandling\EventStore
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EventStoreOptions $options */
        $options = $this->getOptions($serviceLocator, 'eventStore');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Plugin\Zend\Options\EventStore';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param EventStoreOptions $options
     * @return \CQRS\EventHandling\EventStore
     */
    protected function create(ServiceLocatorInterface $sl, EventStoreOptions $options)
    {
        $class = $options->getClass();

        return new $class;
    }
} 
