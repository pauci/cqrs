<?php

namespace CQRS\Serializer;

interface SerializerInterface
{
    /**
     * @param object|array $data
     * @param string $format
     * @return string
     */
    public function serialize($data, $format);

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     * @return object|array
     */
    public function deserialize($data, $type, $format);
}
