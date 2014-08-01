<?php

namespace CQRS\Serializer;

use CQRS\Domain\Message\EventInterface;

interface SerializerInterface
{
    /**
     * @param EventInterface $event
     * @param string $format
     * @return string
     */
    public function serialize(EventInterface $event, $format);

    /**
     * @param string $data
     * @param string $eventClass
     * @param string $format
     * @return EventInterface
     */
    public function deserialize($data, $eventClass, $format);
}
