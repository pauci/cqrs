<?php

namespace CQRSTest\Serializer;

use CQRSTest\Serializer\Model\FloatObject;
use CQRSTest\Serializer\Model\IntegerObject;
use CQRSTest\Serializer\Model\UuidObject;
use CQRSTest\Serializer\Model\StringObject;

class SomeEvent3
{
    /**
     * @var UuidObject
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
        UuidObject $uuid,
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

