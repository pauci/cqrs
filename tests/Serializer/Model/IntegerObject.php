<?php

declare(strict_types=1);

namespace CQRSTest\Serializer\Model;

class IntegerObject
{
    private int $value;

    public static function fromInteger(int $value): self
    {
        return new self($value);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }
}
