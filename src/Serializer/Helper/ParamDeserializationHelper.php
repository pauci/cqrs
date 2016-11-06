<?php

namespace CQRS\Serializer\Helper;

use ReflectionParameter;

final class ParamDeserializationHelper
{
    /**
     * @param array $data
     * @param ReflectionParameter $parameter
     * @return mixed
     */
    public function deserializeParam(array $data, ReflectionParameter $parameter)
    {
        $value = $this->getParamValue($data, $parameter);

        $class = $parameter->getClass();
        if (!$class || ($value === null && $parameter->allowsNull())) {
            return $value;
        }

        return $this->deserializeValue($value, $class->getName());
    }

    /**
     * @param array $data
     * @param ReflectionParameter $parameter
     * @return mixed
     */
    private function getParamValue(array $data, ReflectionParameter $parameter)
    {
        $key = $parameter->getName();
        $value = $this->getValue($data, $key);

        // BC for events serialized with generically named aggregate ID field
        if ($value === null
            && $key !== 'aggregateId'
            && $parameter->getPosition() === 0
            && !$parameter->allowsNull()
        ) {
            $value = $this->getValue($data, 'aggregateId');
        }

        return $value;
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed
     */
    private function getValue(array $data, $key)
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
        $string = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $key);
        return strtolower($string);
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private function deserializeValue($value, $type)
    {
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

        return new $type($value);
    }
}
