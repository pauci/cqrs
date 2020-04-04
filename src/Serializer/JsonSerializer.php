<?php

declare(strict_types=1);

namespace CQRS\Serializer;

use CQRS\Serializer\Helper\ParamDeserializationHelper;
use Ramsey\Uuid\Uuid;
use ReflectionException;
use ReflectionMethod;

class JsonSerializer implements SerializerInterface
{
    /**
     * @param mixed $data
     */
    public function serialize($data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR, 512);
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    public function deserialize(string $data, string $type)
    {
        $value = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (method_exists($type, 'jsonDeserialize')) {
            return $type::jsonDeserialize($value, $this);
        }

        if (is_string($value)) {
            if (method_exists($type, 'fromUuid')) {
                return $type::fromUuid(Uuid::fromString($value));
            }

            if (method_exists($type, 'fromString')) {
                return $type::fromString($value);
            }
        } elseif (is_int($value)) {
            if (method_exists($type, 'fromInteger')) {
                return $type::fromInteger($value);
            }

            if (method_exists($type, 'fromInt')) {
                return $type::fromInt($value);
            }
        } elseif (is_float($value)) {
            if (method_exists($type, 'fromFloat')) {
                return $type::fromFloat($value);
            }
        } elseif (null === $value && method_exists($type, 'unknown')) {
            return $type::unknown();
        }

        if (is_array($value)) {
            $helper = new ParamDeserializationHelper();

            $constructor = new ReflectionMethod($type, '__construct');
            $params = [];
            foreach ($constructor->getParameters() as $parameter) {
                $params[] = $helper->deserializeParam($value, $parameter);
            }

            return new $type(...$params);
        }

        return new $type($value);
    }
}
