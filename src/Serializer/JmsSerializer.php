<?php

namespace CQRS\Serializer;

use JMS\Serializer\Serializer;

class JmsSerializer implements SerializerInterface
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $format = 'json';

    /**
     * @param Serializer $serializer
     * @param string $format
     */
    public function __construct(Serializer $serializer, $format = null)
    {
        $this->serializer = $serializer;

        if (null !== $format) {
            $this->format = $format;
        }
    }

    /**
     * @param object|array $data
     * @return string
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data, $this->format);
    }

    /**
     * @param string $data
     * @param string $type
     * @return object|array
     */
    public function deserialize($data, $type)
    {
        return $this->serializer->deserialize($data, $type, $this->format);
    }
}
