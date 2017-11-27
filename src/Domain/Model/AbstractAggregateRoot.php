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
    use DeletableTrait;

    /**
     * @ORM\Version
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @var int
     */
    private $version;

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
