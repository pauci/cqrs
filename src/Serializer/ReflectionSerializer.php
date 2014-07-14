<?php

namespace CQRS\Serializer;

use DateTime;
use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use ReflectionClass;
use Rhumsaa\Uuid\Uuid;

class ReflectionSerializer implements Serializer
{
    /** @var ReflectionClass[] */
    private $classes = [];

    /**
     * @param object $object
     * @return array
     */
    public function toArray($object)
    {
        if ($object instanceof DateTimeInterface) {
            return [
                'php_class' => get_class($object),
                'time'      => $object->format('Y-m-d H:i:s.u'),
                'timezone'  => $object->getTimezone()->getName()
            ];
        }

        if ($object instanceof Uuid) {
            return [
                'php_class' => 'Rhumsaa\Uuid\Uuid',
                'uuid'      => (string) $object,
            ];
        }

        return $this->extractValuesFromObject($object);
    }

    /**
     * @param array $data
     * @return object
     */
    public function fromArray(array $data)
    {
        switch ($data['php_class']) {
            case 'DateTime':
                return DateTime::createFromFormat('Y-m-d H:i:s.u', $data['time'], new DateTimeZone($data['timezone']));

            case 'DateTimeImmutable':
                return DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $data['time'], new DateTimeZone($data['timezone']));

            case 'Rhumsaa\Uuid\Uuid':
                return Uuid::fromString($data['uuid']);
        }

        return $this->hydrateObjectFromValues($data);
    }

    /**
     * @param object $object
     * @return array
     */
    private function extractValuesFromObject($object)
    {
        $reflectionClass = $this->getReflectionClass(get_class($object));

        $data = [
            'php_class' => $reflectionClass->getName(),
        ];

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);

            if (is_object($value)) {
                $value = $this->toArray($value);
            }

            $data[$property->getName()] = $value;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return object
     */
    private function hydrateObjectFromValues(array $data)
    {
        $reflectionClass = $this->getReflectionClass($data['php_class']);

        $object = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($reflectionClass->getProperties() as $property) {
            $name = $property->getName();
            if (!isset($data[$name])) {
                continue;
            }

            $value = $data[$name];

            if (isset($value['php_class'])) {
                $value = $this->fromArray($value);
            }

            $property->setAccessible(true);
            $property->setValue($object, $value);
        }

        return $object;
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
}
