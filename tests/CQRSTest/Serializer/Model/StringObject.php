<?php

declare(strict_types=1);

namespace CQRSTest\Serializer\Model;

class StringObject
{
    private string $value;

    public static function unknown(): self
    {
        return new self();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private function __construct(string $value = null)
    {
        $this->value = $value;
    }
}
