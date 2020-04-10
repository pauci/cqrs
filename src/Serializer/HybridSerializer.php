<?php

declare(strict_types=1);

namespace CQRS\Serializer;

use CQRS\Serializer\Event\FailedToDeserializeEvent;

final class HybridSerializer implements SerializerInterface
{
    private JsonSerializer $jsonSerializer;

    /**
     * @var string[]
     */
    private array $typeMap;

    public function __construct(JsonSerializer $jsonSerializer, array $typeMap)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->typeMap = $typeMap;
    }

    /**
     * @param mixed $data
     */
    public function serialize($data): string
    {
        return $this->jsonSerializer->serialize($data);
    }

    /**
     * @return mixed
     */
    public function deserialize(string $data, string $type)
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

    private function translateType(string $type): string
    {
        return array_key_exists($type, $this->typeMap) ? $this->typeMap[$type] : $type;
    }
}
