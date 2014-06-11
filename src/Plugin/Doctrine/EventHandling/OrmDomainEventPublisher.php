<?php

namespace CQRS\Plugin\Doctrine\EventHandling;

use CQRS\Domain\AggregateRoot;
use CQRS\EventHandling\EventBus;
use CQRS\EventHandling\EventPublisher;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class OrmDomainEventPublisher implements
    EventPublisher,
    EventSubscriber
{
    /** @var AggregateRoot[] */
    private $aggregateRoots = [];

    /** @var EventBus */
    private $eventBus;

    /**
     * @param EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function publishEvents()
    {
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush
        ];
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
        $entityManager = $event->getEntityManager();

        foreach ($this->aggregateRoots as $aggregateRoot) {
            $class = $entityManager->getClassMetadata(get_class($aggregateRoot));

            foreach ($aggregateRoot->pullDomainEvents() as $domainEvent) {
                $domainEvent->aggregateType = $class->name;
                $domainEvent->aggregateId   = $class->getSingleIdReflectionProperty()->getValue($aggregateRoot);

                $this->eventBus->publish($domainEvent);
            }
        }
        $this->aggregateRoots = array();
    }

    /**
     * @param LifecycleEventArgs $event
     */
    private function keepAggregateRoots(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof AggregateRoot) {
            return;
        }

        $this->aggregateRoots[] = $entity;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @return string
     */
    private function getAggregateType(ClassMetadata $classMetadata)
    {
        $pos = strrpos($classMetadata->name, '\\');

        if ($pos === false) {
            return $classMetadata->name;
        }

        return substr($classMetadata->name, $pos + 1);
    }

    /**
     * @return DateTime
     */
    private function getMicrosecondsNow()
    {
        return DateTime::createFromFormat('u', substr(microtime(), 2, 6));
    }
}
