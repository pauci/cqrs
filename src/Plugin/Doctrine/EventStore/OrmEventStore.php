<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\EventHandling\DomainEvent;
use CQRS\EventStore\EventStore;

class OrmEventStore implements EventStore
{
    /** @var string */
    private $entityClass;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    public function store(DomainEvent $event)
    {
        $entity = new $this->entityClass($event);

        $this->entityManager->persist($entity);
    }
}
