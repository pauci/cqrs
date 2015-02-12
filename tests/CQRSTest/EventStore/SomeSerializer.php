<?php

namespace CQRSTest\EventStore;

use CQRS\Serializer\SerializerInterface;

class SomeSerializer implements SerializerInterface
{
    /**
     * @param object|array $data
     * @param string $format
     * @return string
     */
    public function serialize($data, $format)
    {
        return '{}';
    }

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     * @return object|array
     */
    public function deserialize($data, $type, $format)
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
