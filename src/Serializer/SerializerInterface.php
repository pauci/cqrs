<?php

declare(strict_types=1);

namespace CQRS\Serializer;

interface SerializerInterface
{
    public function serialize(object $data): string;

    public function deserialize(string $data, string $type): object;
}
