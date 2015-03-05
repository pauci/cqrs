<?php

namespace CQRSTest\EventStore;

use CQRS\Serializer\SerializerInterface;

class SomeSerializer implements SerializerInterface
{
    /**
     * @param object|array $data
     * @return string
     */
    public function serialize($data)
    {
        return '{}';
    }

    /**
     * @param string $data
     * @param string $type
     * @return object|array
     */
    public function deserialize($data, $type)
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
