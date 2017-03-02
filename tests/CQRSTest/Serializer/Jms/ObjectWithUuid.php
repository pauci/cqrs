<?php

namespace CQRSTest\Serializer\Jms;

use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ObjectWithUuid
{
    /**
     * @JMS\Type("Ramsey\Uuid\Uuid")
     */
    private $uuid;

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function generate()
    {
        return new self(Uuid::uuid4());
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public static function fromUuid(UuidInterface $uuid) {
        return new self($uuid);
    }
}
