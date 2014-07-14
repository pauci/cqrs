<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\EventStore\EventStoreInterface;
use CQRS\Plugin\Doctrine\EventStore\DbalEventStore;
use CQRS\Plugin\Zend\Options\EventStore as EventStoreOptions;
use CQRS\Serializer\ReflectionSerializer;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventStoreFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return EventStoreInterface
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
     * @return EventStoreInterface
     */
    protected function create(ServiceLocatorInterface $sl, EventStoreOptions $options)
    {
        $class = $options->getClass();

        if ($class == 'CQRS\Plugin\Doctrine\EventStore\DbalEventStore') {
            $serializer = new ReflectionSerializer();
            /** @var \Doctrine\DBAL\Connection $connection */
            $connection = $sl->get($options->getDbalConnection());
            return new DbalEventStore($serializer, $connection);
        }

        return new $class;
    }
} 
