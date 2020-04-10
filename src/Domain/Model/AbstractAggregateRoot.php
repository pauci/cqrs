<?php

declare(strict_types=1);

namespace CQRS\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractAggregateRoot implements AggregateRootInterface, DeletableInterface
{
    use AggregateRootTrait;
    use VersionedTrait;
    use DeletableTrait;
}
