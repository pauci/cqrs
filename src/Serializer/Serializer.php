<?php

namespace CQRS\Serializer;

interface Serializer
{
    /**
     * @param object $object
     * @return array
     */
    public function toArray($object);

    /**
     * @param array $data
     * @return object
     */
    public function fromArray(array $data);
}
