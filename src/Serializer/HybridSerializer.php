<?php

namespace CQRS\Serializer;

use CQRS\Serializer\Event\FailedToDeserializeEvent;

final class HybridSerializer implements SerializerInterface
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var array
     */
    private $typeMap;

    public function __construct(JsonSerializer $jsonSerializer, array $typeMap)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->typeMap = $typeMap;
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return $this->jsonSerializer->serialize($data);
    }

    /**
     * @param string $data
     * @param string $type
     * @return mixed
     */
    public function deserialize($data, $type)
    {
        $type = $this->translateType($type);

        if (!class_exists($type)) {
            return new FailedToDeserializeEvent(sprintf('Class %s not found', $type), $type, $data);
        }

        // @todo remove try-catch after implementation of event-processing error logging
        try {
            return $this->jsonSerializer->deserialize($data, $type);
        } catch (\Throwable $e) {
            $error = (string) get_class($e);
            return new FailedToDeserializeEvent($error, $type, $data);
        }
    }

    private function translateType($type)
    {
        return array_key_exists($type, $this->typeMap) ? $this->typeMap[$type] : $type;
    }
}
