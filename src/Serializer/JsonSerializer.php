<?php

namespace CQRS\Serializer;

class JsonSerializer implements SerializerInterface
{
    /**
     * @param object|array $data
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($data);
    }

    /**
     * @param string $data
     * @param string $type
     * @return object|array
     */
    public function deserialize($data, $type)
    {
        $data = json_decode($data, true);

        return method_exists($type, 'jsonDeserialize')
            ? $type::jsonDeserialize($data)
            : new $type($data);
    }
}
