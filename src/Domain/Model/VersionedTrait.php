<?php

declare(strict_types=1);

namespace CQRS\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

trait VersionedTrait
{
    /**
     * @ORM\Version
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $version;

    public function getVersion(): int
    {
        return $this->version;
    }
}
