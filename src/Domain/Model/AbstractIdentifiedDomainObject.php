<?php

declare(strict_types=1);

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
     */
    private int $id;

    protected function getId(): int
    {
        return $this->id;
    }
}
