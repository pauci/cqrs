<?php

namespace CQRS\Plugin\Doctrine\Domain;

use CQRS\Domain\Model\AbstractAggregateRoot;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class AggregateRootMetadataListener implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $args->getClassMetadata();

        if (!$classMetadata->name instanceof AbstractAggregateRoot) {
            return;
        }

        $idEntityName = $classMetadata->name . 'Id';

        $classMetadata->mapOneToOne([
            'fieldName'    => 'id',
            'targetEntity' => $idEntityName
        ]);
    }
}
