<?php

namespace CQRS\Domain\Message;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Countable;
use CQRS\Exception\RuntimeException;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

class Metadata implements IteratorAggregate, ArrayAccess, Countable, Serializable, JsonSerializable
{
    /**
     * @var self
     */
    private static $emptyInstance;

    /**
     * @var array
     */
    private $values;

    /**
     * @return Metadata
     */
    public static function emptyInstance()
    {
        if (static::$emptyInstance === null) {
            static::$emptyInstance = new static();
        }
        return static::$emptyInstance;
    }

    public static function resetEmptyInstance()
    {
        static::$emptyInstance = null;
    }

    /**
     * @param array|self $metadata
     * @return Metadata
     */
    public static function from($metadata = null)
    {
        if ($metadata instanceof static) {
            return $metadata;
        }
        if ($metadata === null || $metadata === []) {
            return static::emptyInstance();
        }
        return new static($metadata);
    }

    /**
     * @param array $data
     * @return Metadata
     */
    public static function jsonDeserialize(array $data)
    {
        return new static($data);
    }

    /**
     * @param array $values
     */
    private function __construct(array $values = [])
    {
        ksort($values);
        $this->values = $values;
    }

    /**
     * @return ArrayObject
     */
    public function jsonSerialize()
    {
        return new ArrayObject($this->toArray());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->values[$offset]) ? $this->values[$offset] : null;
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Event metadata is immutable.');
    }

    /**
     * @param string $offset
     * @throws RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Event metadata is immutable.');
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->values);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->values = unserialize($serialized);
    }

    /**
     * Returns a Metadata instance containing values of this, combined with the given additionalMetadata.
     * If any entries have identical keys, the values from the additionalMetadata will take precedence.
     *
     * @param Metadata $additionalMetadata
     * @return self
     */
    public function mergedWith(Metadata $additionalMetadata)
    {
        $values = array_merge($this->values, $additionalMetadata->values);

        if ($values === $this->values) {
            return $this;
        }

        return new static($values);
    }

    /**
     * Returns a Metadata instance with the items with given keys removed. Keys for which there is no
     * assigned value are ignored.
     *
     * This Metadata instance is not influenced by this operation.
     *
     * @param array $keys
     * @return self
     */
    public function withoutKeys(array $keys)
    {
        $values = array_diff_key($this->values, array_flip($keys));

        if ($values === $this->values) {
            return $this;
        }

        return new static($values);
    }
}
