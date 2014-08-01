<?php

namespace CQRS\Serializer;

use CQRS\Domain\Message\EventInterface;
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
     * @param EventInterface $event
     * @param string $format
     * @return string
     */
    public function serialize(EventInterface $event, $format)
    {
        return $this->serializer->serialize($event, $format);
    }

    /**
     * @param string $data
     * @param string $eventClass
     * @param string $format
     * @return EventInterface
     */
    public function deserialize($data, $eventClass, $format)
    {
        return $this->serializer->deserialize($data, $eventClass, $format);
    }
}
