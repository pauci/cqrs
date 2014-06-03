<?php

namespace CQRS\Commanding;

use Doctrine\ORM\EntityManager;

class OrmTransactionalCommandBusWrapper implements CommandBus
{
    /** @var EntityManager */
    private $entityManager;

    /** @var CommandBus */
    private $next;

    /**
     * @param EntityManager $entityManager
     * @param CommandBus $next
     */
    public function __construct(EntityManager $entityManager, CommandBus $next)
    {
        $this->entityManager = $entityManager;
        $this->next = $next;
    }

    /**
     * @param object $command
     * @throws \Exception
     */
    public function handle($command)
    {
        $this->entityManager->beginTransaction();

        try {
            $this->next->handle($command);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch(\Exception $e) {
            $this->entityManager->rollBack();
            throw $e;
        }
    }
}
