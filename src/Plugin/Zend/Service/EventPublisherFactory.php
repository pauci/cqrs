<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\Plugin\Doctrine\EventHandling\OrmDomainEventPublisher;
use CQRS\Plugin\Zend\Options\EventPublisher as EventPublisherOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventPublisherFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\EventHandling\Publisher\EventPublisherInterface
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
     * @return \CQRS\EventHandling\EventBusInterface
     */
    protected function create(ServiceLocatorInterface $sl, EventPublisherOptions $options)
    {
        $class = $options->getClass();

        /** @var \CQRS\EventHandling\EventBusInterface $eventBus */
        $eventBus = $sl->get($options->getEventBus());

        $eventPublisher = new $class($eventBus);

        if ($eventPublisher instanceof OrmDomainEventPublisher) {
            /** @var \Doctrine\ORM\EntityManager $entityManager */
            $entityManager = $sl->get($options->getOrmEntityManager());
            $entityManager->getEventManager()->addEventSubscriber($eventPublisher);
        }

        return $eventPublisher;
    }
} 
