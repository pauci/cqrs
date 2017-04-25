<?php

namespace CQRSTest\Serializer;

use CQRSTest\Serializer\Jms\FloatObject;
use CQRSTest\Serializer\Jms\IntegerObject;
use CQRSTest\Serializer\Jms\ObjectWithUuid;
use CQRSTest\Serializer\Jms\StringObject;

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
    /**
     * @var StringObject
     */
    private $string1;
    /**
     * @var StringObject
     */
    private $string2;

    public function __construct(
        ObjectWithUuid $uuid,
        IntegerObject $int,
        StringObject $string1,
        StringObject $string2 = null
    ) {
        $this->uuid = $uuid;
        $this->int = $int;
        $this->string1 = $string1;
        $this->string2 = $string2;
    }
}

