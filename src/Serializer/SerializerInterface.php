<?php

namespace CQRS\Serializer;

interface SerializerInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data);

    /**
     * @param string $data
     * @param string $type
     * @return mixed
     */
    public function deserialize($data, $type);
}
