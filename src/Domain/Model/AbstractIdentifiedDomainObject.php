<?php

namespace CQRS\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractIdentifiedDomainObject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @return int
     */
    protected function getId()
    {
        return $this->id;
    }
}
