<?php

namespace CQRS\Serializer;

use CQRS\Domain\Message\AbstractDomainEvent;
use CQRS\Domain\Message\AbstractEvent;
use CQRS\Domain\Message\DomainEventInterface;
use CQRS\Domain\Message\EventInterface;
use DateTime;
use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use ReflectionClass;
use ReflectionProperty;
use Rhumsaa\Uuid\Uuid;

class ReflectionSerializer implements SerializerInterface
{
    /** @var ReflectionClass[] */
    private $classes = [];

    /** @var ReflectionProperty[][] */
    private $reflectionProperties = [];

    /**
     * @param EventInterface $event
     * @param string $format
     * @return string
     */
    public function serialize(EventInterface $event, $format)
    {
        return json_encode($this->toPhpClassArray($event));
    }

    /**
     * @param string $data
     * @param string $eventClass
     * @param string $format
     * @return string
     */
    public function deserialize($data, $eventClass, $format)
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
        if ($object instanceof EventInterface) {
            $data = [
                'id'        => $object->getId(),
                'timestamp' => $object->getTimestamp(),
            ];

            if ($object instanceof DomainEventInterface) {
                $data['aggregate_type'] = $object->getAggregateType();
                $data['aggregate_id']   = $object->getAggregateId();
            }

            $data['event_name'] = $object->getEventName();
            $data['payload']    = $this->extractValuesFromObject($object);
            foreach ($data['payload'] as &$value) {
                if (is_object($value)) {
                    $value = $this->toPhpClassArray($value);
                }
            }
            return $data;
        }

        if ($object instanceof DateTimeInterface) {
            return [
                'time'     => $object->format('Y-m-d H:i:s.u'),
                'timezone' => $object->getTimezone()->getName()
            ];
        }

        if ($object instanceof Uuid) {
            return [
                'uuid' => (string) $object,
            ];
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
                return DateTime::createFromFormat('Y-m-d H:i:s.u', $data['time'], new DateTimeZone($data['timezone']));

            case DateTimeImmutable::class:
                return DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $data['time'], new DateTimeZone($data['timezone']));

            case Uuid::class:
                return Uuid::fromString($data['uuid']);
        }

        $reflectionClass = $this->getReflectionClass($className);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        if (is_subclass_of($className, AbstractEvent::class)) {
            if (isset($data['event_name'])) {
                $data['eventName'] = $data['event_name'];
            }
            $this->hydrateObjectFromValues($object, $data, AbstractEvent::class);

            if (is_subclass_of($className, AbstractDomainEvent::class)) {
                if (isset($data['aggregate_id'])) {
                    $data['aggregateId'] = $data['aggregate_id'];
                }
                if (isset($data['aggregate_type'])) {
                    $data['aggregateType'] = $data['aggregate_type'];
                }
                $this->hydrateObjectFromValues($object, $data, AbstractDomainEvent::class);
            }

            $data = $data['payload'];
        }

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
