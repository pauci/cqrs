<?php

namespace CQRSTest\Serializer\Jms;

use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

class ObjectWithUuid
{
    /**
     * @JMS\Type("Ramsey\Uuid\Uuid")
     */
    protected $uuid;

    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function setUuid(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}
