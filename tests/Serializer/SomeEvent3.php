<?php

declare(strict_types=1);

namespace CQRSTest\Serializer;

use CQRSTest\Serializer\Model\IntegerObject;
use CQRSTest\Serializer\Model\UuidObject;
use CQRSTest\Serializer\Model\StringObject;

class SomeEvent3
{
    private UuidObject $uuid;

    private IntegerObject $int;

    private StringObject $string1;

    private ?StringObject $string2;

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
