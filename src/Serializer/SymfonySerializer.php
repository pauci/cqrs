<?php

declare(strict_types=1);

namespace CQRS\Serializer;

use CQRS\Exception;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

final class SymfonySerializer implements SerializerInterface
{
    private SymfonySerializerInterface $serializer;

    private string $format;

    private array $context;

    public function __construct(SymfonySerializerInterface $serializer, string $format, array $context = [])
    {
        $this->serializer = $serializer;
        $this->format = $format;
        $this->context = $context;
    }


    public function serialize(object $data): string
    {
        return $this->serializer->serialize($data, $this->format, $this->context);
    }

    public function deserialize(string $data, string $type): object
    {
        $object = $this->serializer->deserialize($data, $type, $this->format, $this->context);

        if (!is_object($object)) {
            throw new Exception\RuntimeException(sprintf(
                'Expected deserialized data to be an object, got "%s".',
                get_debug_type($object)
            ));
        }

        return $object;
    }
}
