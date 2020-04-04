<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;
use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\ORM\Events;

class DoctrineIdentityMap extends SimpleIdentityMap implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::prePersist,
            Events::preRemove,
            Events::onClear,
        ];
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof AggregateRootInterface) {
            $this->add($entity);
        }
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof AggregateRootInterface) {
            $this->add($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof AggregateRootInterface) {
            $this->remove($entity);
        }
    }

    public function onClear(OnClearEventArgs $args): void
    {
        if ($args->clearsAllEntities()) {
            $this->clear();
        }
    }
}
