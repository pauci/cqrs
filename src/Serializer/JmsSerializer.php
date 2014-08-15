<?php

namespace CQRS\Serializer;

use JMS\Serializer\Serializer;

class JmsSerializer implements SerializerInterface
{
    /** @var Serializer */
    private $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param object|array $data
     * @param string $format
     * @return string
     */
    public function serialize($data, $format)
    {
        return $this->serializer->serialize($data, $format);
    }

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     * @return object|array
     */
    public function deserialize($data, $type, $format)
    {
        return $this->serializer->deserialize($data, $type, $format);
    }
}
