<?php

namespace CQRS\Serializer;

use DateTime;
use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;
use ReflectionProperty;

class ReflectionSerializer implements SerializerInterface
{
    /**
     * @var ReflectionClass[]
     */
    private $classes = [];

    /**
     * @var ReflectionProperty[][]
     */
    private $reflectionProperties = [];

    /**
     * @param object $data
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($this->toPhpClassArray($data));
    }

    /**
     * @param string $data
     * @param string $type
     * @return object
     */
    public function deserialize($data, $type)
    {
        return $this->fromArray(json_decode($data, true));
    }


    /**
     * @param object $object
     * @return array
     */
    private function toPhpClassArray($object)
    {
        $data = $this->toArray($object);

        foreach ($data as &$value) {
            if (is_object($value)) {
                $value = $this->toPhpClassArray($value);
            }
        }

        return array_merge([
            'php_class' => get_class($object)
        ], $data);
    }

    /**
     * @param object $object
     * @return array
     */
    private function toArray($object)
    {
        if ($object instanceof DateTimeInterface) {
            return ['time' => $object->format('Y-m-d\TH:i:s.uO')];
        }

        if ($object instanceof UuidInterface) {
            return ['uuid' => (string) $object];
        }

        return $this->extractValuesFromObject($object);
    }

    /**
     * @param object $object
     * @return array
     */
    private function extractValuesFromObject($object)
    {
        $data = [];
        foreach ($this->getReflectionProperties(get_class($object)) as $property) {
            $data[$property->getName()] = $property->getValue($object);
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array|object
     */
    private function fromArray(array $data)
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->fromArray($value);
            }
        }

        if (isset($data['php_class'])) {
            return $this->toObject($data['php_class'], $data);
        }

        return $data;
    }

    /**
     * @param string $className
     * @param array $data
     * @return object
     */
    private function toObject($className, array $data)
    {
        switch ($className) {
            case DateTime::class:
            case DateTimeImmutable::class:
                $timezone = isset($data['timezone']) ? new DateTimeZone($data['timezone']) : null;
                return new $className($data['time'], $timezone);

            case Uuid::class:
                return Uuid::fromString($data['uuid']);
        }

        $reflectionClass = $this->getReflectionClass($className);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        $this->hydrateObjectFromValues($object, $data, $className);
        return $object;
    }

    /**
     * @param object $object
     * @param array $data
     * @param string $className
     */
    private function hydrateObjectFromValues($object, array $data, $className)
    {
        foreach ($this->getReflectionProperties($className) as $property) {
            $name = $property->getName();
            if (!isset($data[$name])) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($object, $data[$name]);
        }
    }

    /**
     * @param string $className
     * @return ReflectionClass
     */
    private function getReflectionClass($className)
    {
        if (!isset($this->classes[$className])) {
            $this->classes[$className] = new ReflectionClass($className);
        }

        return $this->classes[$className];
    }

    /**
     * @param string $className
     * @return ReflectionProperty[]
     */
    private function getReflectionProperties($className)
    {
        if (!isset($this->reflectionProperties[$className])) {
            $reflectionClass = $this->getReflectionClass($className);
            $this->reflectionProperties[$className] = $reflectionClass->getProperties();
            foreach ($this->reflectionProperties[$className] as $reflectionProperty) {
                $reflectionProperty->setAccessible(true);
            }
        }

        return $this->reflectionProperties[$className];
    }
}
