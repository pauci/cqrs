<?php

namespace CQRS\Domain\Message;

use ArrayAccess;
use ArrayIterator;
use Countable;
use CQRS\Exception\RuntimeException;
use IteratorAggregate;
use Serializable;

class Metadata implements IteratorAggregate, ArrayAccess, Countable, Serializable
{
    /** @var array */
    private $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        ksort($items);
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return self
     */
    public function mergedWith(array $items)
    {
        $items = array_merge($this->items, $items);

        if ($items == $this->items) {
            return $this;
        }

        return new self($items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->raiseImmutabilityException();
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->raiseImmutabilityException();
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->items = unserialize($serialized);
    }

    private function raiseImmutabilityException()
    {
        throw new RuntimeException('Event metadata is immutable.');
    }
}
