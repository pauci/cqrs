<?php

namespace CQRS\Domain\SuperType;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @property-read int $id
 */
abstract class AbstractId
{
    /**
     * @ORM\Column(type = "integer")
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     * @return int
     * @throws \RuntimeException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->id;
        }

        throw new \RuntimeException(sprintf('Trying to access invalid property "%s"', $name));
    }
}
