<?php

declare(strict_types=1);

namespace CQRSTest\Serializer\Model;

class FloatObject
{
    private float $value;

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    private function __construct(float $value)
    {
        $this->value = $value;
    }
}
