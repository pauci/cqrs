<?php

namespace CQRS\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property-read int $id
 */
abstract class Id extends IdentifiedValueObject
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
