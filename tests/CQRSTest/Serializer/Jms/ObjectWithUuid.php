<?php

namespace CQRSTest\Serializer\Jms;

use JMS\Serializer\Annotation as JMS;
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

    public function getUuid()
    {
        return $this->uuid;
    }
}
