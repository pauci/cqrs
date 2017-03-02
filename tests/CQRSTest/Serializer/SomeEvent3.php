<?php

namespace CQRSTest\Serializer;

use CQRSTest\Serializer\Jms\IntegerObject;
use CQRSTest\Serializer\Jms\ObjectWithUuid;

class SomeEvent3
{
    /**
     * @var ObjectWithUuid
     */
    private $uuid;
    /**
     * @var IntegerObject
     */
    private $int;

    public function __construct(ObjectWithUuid $uuid, IntegerObject $int)
    {
        $this->uuid = $uuid;
        $this->int = $int;
    }
}

