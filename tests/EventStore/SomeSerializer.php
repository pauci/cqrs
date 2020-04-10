<?php

declare(strict_types=1);

namespace CQRSTest\EventStore;

use CQRS\Serializer\SerializerInterface;

class SomeSerializer implements SerializerInterface
{
    /**
     * @param mixed $data
     */
    public function serialize($data): string
    {
        return '{}';
    }

    /**
     * @return mixed
     */
    public function deserialize(string $data, string $type)
    {
        switch ($type) {
            case SomeEvent::class:
                return new SomeEvent();

            case 'array':
                return [];
        }

        return null;
    }
}
