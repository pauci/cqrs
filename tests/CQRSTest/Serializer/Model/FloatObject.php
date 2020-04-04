<?php

namespace CQRSTest\Serializer\Model;


class FloatObject
{
    private $id;

    private function __construct(float $value)
    {
        $this->id = $value;
    }

    public static function fromFloat(float $value)
    {
        return new self($value);
    }
}
