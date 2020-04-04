<?php

namespace CQRSTest\Serializer\Model;


class IntObject
{
    private $id;

    private function __construct(int $value)
    {
        $this->id = $value;
    }

    public static function fromInt(int $value)
    {
        return new self($value);
    }
}
