<?php
declare(strict_types=1);

namespace CQRS\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractEventSourcedAggregateRoot implements AggregateRootInterface, DeletableInterface
{
    use EventSourcedAggregateRootTrait;
    use VersionedTrait;
    use DeletableTrait;
}
