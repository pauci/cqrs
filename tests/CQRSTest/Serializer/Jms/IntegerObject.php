<?php

namespace CQRSTest\Serializer\Jms;


class IntegerObject
{
    private $id;

    private function __construct(int $value)
    {
        $this->id = $value;
    }

    public static function fromInteger(int $value)
    {
        return new self($value);
    }
}
