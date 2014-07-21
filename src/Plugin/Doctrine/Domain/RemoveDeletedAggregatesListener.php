<?php

namespace CQRS\Plugin\Doctrine\Domain;

use CQRS\Domain\Model\AggregateRootInterface;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;

class RemoveDeletedAggregatesListener implements EventSubscriber
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        // Subscribe itself with doctrine event manager
        /** @var EventManager $eventManager */
        $eventManager = $this->entityManager->getEventManager();
        $eventManager->addEventSubscriber($this);
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preFlush,
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
}
