<?php

namespace CQRSTest\Serializer\Model;


class StringObject
{
    private $id;

    private function __construct(string $value = null)
    {
        $this->id = $value;
    }

    public static function fromString(string $value)
    {
        return new self($value);
    }

    public static function unknown()
    {
        return new self();
    }
}
