<?php

namespace CQRS\Plugin\Doctrine\EventHandling;

use CQRS\Domain\Model\AggregateRootInterface;
use CQRS\EventHandling\EventBusInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

class OrmDomainEventPublisher implements
    EventPublisherInterface,
    EventSubscriber
{
    /** @var EventBusInterface */
    private $eventBus;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var AggregateRootInterface[] */
    private $aggregateRoots = [];

    /** @var bool */
    private $isPublishing = false;

    /**
     * @param EventBusInterface $eventBus
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EventBusInterface $eventBus, EntityManagerInterface $entityManager)
    {
        $this->eventBus      = $eventBus;
        $this->entityManager = $entityManager;

        /** @var \Doctrine\Common\EventManager $eventManager */
        $eventManager = $entityManager->getEventManager();
        $eventManager->addEventSubscriber($this);
    }

    public function publishEvents()
    {
        $this->isPublishing = true;

        // Events will be published within postFlush doctrine event
        $this->entityManager->flush();

        $this->isPublishing = false;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preFlush,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush
        ];
    }

    /**
     * Remove entities marked as deleted
     *
     * @param PreFlushEventArgs $event
     */
    public function preFlush(PreFlushEventArgs $event)
    {
        $entityManager = $event->getEntityManager();
        $uow = $entityManager->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $class => $entities) {
            foreach ($entities as $entity) {
                if ($entity instanceof AggregateRootInterface) {
                    if ($entity->isDeleted()) {
                        $entityManager->remove($entity);
                    }
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $this->keepAggregateRoots($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->keepAggregateRoots($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event)
    {
        $this->keepAggregateRoots($event);
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        // Publish only if flush was called within publishEvents() method
        if (!$this->isPublishing) {
            return;
        }

        foreach ($this->aggregateRoots as $aggregateRoot) {
            foreach ($aggregateRoot->pullDomainEvents() as $domainEvent) {
                $this->eventBus->publish($domainEvent);
            }
        }
        $this->aggregateRoots = [];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    private function keepAggregateRoots(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof AggregateRootInterface) {
            return;
        }

        $this->aggregateRoots[] = $entity;
    }
}
