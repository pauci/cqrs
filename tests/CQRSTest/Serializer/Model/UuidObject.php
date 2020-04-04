<?php

declare(strict_types=1);

namespace CQRSTest\Serializer\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidObject
{
    private UuidInterface $uuid;

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromUuid(UuidInterface $uuid): self
    {
        return new self($uuid);
    }

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
