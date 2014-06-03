<?php

namespace CQRS\Domain\SuperType;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class IdentifiedDomainObject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     * @var int
     */
    private $id;

    protected function id()
    {
        return $this->id;
    }
} 
