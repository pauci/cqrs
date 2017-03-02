<?php

namespace CQRS\Serializer;

use ReflectionMethod;
use ReflectionParameter;

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
        } elseif (is_float($value)) {
            if (method_exists($type, 'fromFloat')) {
                return $type::fromFloat($value);
            }
        } elseif (null === $value && method_exists($type, 'unknown')) {
            return $type::unknown();
        }

        if (is_array($value)) {
            $constructor = new ReflectionMethod($type, '__construct');
            $params = [];
            foreach ($constructor->getParameters() as $parameter) {
                $params[] = $this->deserializeParamValue($value, $parameter);
            }

            return new $type(...$params);
        }

        return new $type($value);
    }

    /**
     * @param array $data
     * @param ReflectionParameter $parameter
     * @return mixed
     */
    private function deserializeParamValue(array $data, ReflectionParameter $parameter)
    {
        $value = $this->getValueByParam($data, $parameter);

        $class = $parameter->getClass();
        if (!$class || ($value === null && $parameter->allowsNull())) {
            return $value;
        }

        return $this->deserialize($value, $class->getName());
    }

    /**
     * @param array $data
     * @param ReflectionParameter $parameter
     * @return mixed
     */
    private function getValueByParam(array $data, ReflectionParameter $parameter)
    {
        $key = $parameter->getName();
        $value = $this->getValueByKey($data, $key);

        // BC for events serialized with generically named aggregate ID field
        if ($value === null
            && $key !== 'aggregateId'
            && $parameter->getPosition() === 0
            && !$parameter->allowsNull()
        ) {
            $value = $this->getValueByKey($data, 'aggregateId');
        }

        return $value;
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed
     */
    private function getValueByKey(array $data, $key)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }

        if (isset($data[$this->camelCaseToUnderscore($key)])) {
            return $data[$this->camelCaseToUnderscore($key)];
        }

        return null;
    }

    private function camelCaseToUnderscore($key)
    {
        $string = preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $key);
        return strtolower($string);
    }
}
