<?php

namespace CQRS\Serializer;

use CQRS\Serializer\Helper\ParamDeserializationHelper;
use ReflectionMethod;

class JsonSerializer implements SerializerInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($data);
    }

    /**
     * @param string $data
     * @param string $type
     * @return mixed
     */
    public function deserialize($data, $type)
    {
        $value = json_decode($data, true);

        if (method_exists($type, 'jsonDeserialize')) {
            return $type::jsonDeserialize($value, $this);
        }

        if (is_string($value)) {
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
        } elseif (null === $value && method_exists($type, 'unknown')) {
            return $type::unknown();
        }

        if (is_array($value)) {
            $helper = new ParamDeserializationHelper();

            $params = [];
            $constructor = new ReflectionMethod($type, '__construct');
            foreach ($constructor->getParameters() as $parameter) {
                $params[] = $helper->deserializeParam($value, $parameter);
            }

            return new $type(...$params);
        }

        return new $type($value);
    }
}
