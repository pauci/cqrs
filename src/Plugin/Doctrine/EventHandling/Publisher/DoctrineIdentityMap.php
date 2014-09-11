<?php

namespace CQRS\Plugin\Doctrine\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;
use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\ORM\Events;

class DoctrineIdentityMap extends SimpleIdentityMap implements EventSubscriber
{
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $entityManager->getEventManager()
            ->addEventSubscriber($this);
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
            Events::prePersist,
            Events::preRemove,
            Events::onClear,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AggregateRootInterface) {
            $this->add($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AggregateRootInterface) {
            $this->add($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AggregateRootInterface) {
            $this->remove($entity);
        }
    }

    /**
     * @param OnClearEventArgs $args
     */
    public function onClear(OnClearEventArgs $args)
    {
        if ($args->clearsAllEntities()) {
            $this->clear();
        }
    }
}
