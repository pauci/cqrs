<?php

namespace CQRS\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @property-read int $id
 */
abstract class AbstractId extends IdentifiedValueObject
{
    /**
     * @param string $name
     * @return int
     * @throws \RuntimeException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->getId();
        }

        throw new \RuntimeException(sprintf('Trying to access invalid property "%s"', $name));
    }
}
