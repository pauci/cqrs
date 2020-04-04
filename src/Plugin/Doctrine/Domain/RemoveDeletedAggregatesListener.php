<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\Domain;

use CQRS\Domain\Model\DeletableInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;

class RemoveDeletedAggregatesListener implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preFlush,
        ];
    }

    /**
     * Remove entities marked as deleted
     */
    public function preFlush(PreFlushEventArgs $event): void
    {
        $entityManager = $event->getEntityManager();
        $uow = $entityManager->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $class => $entities) {
            foreach ($entities as $entity) {
                if ($entity instanceof DeletableInterface && $entity->isDeleted()) {
                    $entityManager->remove($entity);
                }
            }
        }
    }
}
