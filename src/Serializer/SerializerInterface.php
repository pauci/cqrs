<?php

namespace CQRS\Serializer;

interface SerializerInterface
{
    /**
     * @param object|array $data
     * @return string
     */
    public function serialize($data);

    /**
     * @param string $data
     * @param string $type
     * @return object|array
     */
    public function deserialize($data, $type);
}
