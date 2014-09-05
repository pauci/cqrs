<?php

namespace CQRS\Plugin\Doctrine\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;
use CQRS\EventHandling\Publisher\IdentityMapInterface;
use Doctrine\ORM\EntityManager;

class DoctrineIdentityMap implements IdentityMapInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return AggregateRootInterface[]
     */
    public function all()
    {
        $aggregateRoots = [];
        $uow            = $this->entityManager->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $class => $entities) {
            foreach ($entities as $entity) {
                if (!($entity instanceof AggregateRootInterface)) {
                    break;
                }

                $aggregateRoots[] = $entity;
            }
        }

        return $aggregateRoots;
    }
}
