<?php

declare(strict_types=1);

namespace CQRS\Serializer;

interface SerializerInterface
{
    /**
     * @param mixed $data
     */
    public function serialize($data): string;

    /**
     * @return mixed
     */
    public function deserialize(string $data, string $type);
}
