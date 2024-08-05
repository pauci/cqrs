<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;
use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
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

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AggregateRootInterface) {
            $this->add($entity);
        }
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AggregateRootInterface) {
            $this->add($entity);
        }
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AggregateRootInterface) {
            $this->remove($entity);
        }
    }

    public function onClear(OnClearEventArgs $args): void
    {
        $this->clear();
    }
}
