<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use ArrayAccess;
use ArrayIterator;
use Countable;
use CQRS\Exception\RuntimeException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class Metadata implements IteratorAggregate, ArrayAccess, Countable, JsonSerializable
{
    private array $data;

    /**
     * @param self|array|null $metadata
     */
    public static function from($metadata = null): self
    {
        if ($metadata instanceof self) {
            return $metadata;
        }
        if ($metadata === null) {
            return new static();
        }
        return new static($metadata);
    }

    public static function jsonDeserialize(array $data): self
    {
        return new static($data);
    }

    final private function __construct(array $data = [])
    {
        ksort($data);
        $this->data = $data;
    }

    public function jsonSerialize(): object
    {
        return (object) $this->data;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws RuntimeException
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Event metadata is immutable.');
    }

    /**
     * @param string $offset
     * @throws RuntimeException
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Event metadata is immutable.');
    }

    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Returns a Metadata instance containing values of this, combined with the given additionalMetadata.
     * If any entries have identical keys, the values from the additionalMetadata will take precedence.
     */
    public function mergedWith(Metadata $additionalMetadata): self
    {
        $values = array_merge($this->data, $additionalMetadata->data);

        if ($values === $this->data) {
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
     * @param string[] $keys
     */
    public function withoutKeys(array $keys): self
    {
        $values = array_diff_key($this->data, array_flip($keys));

        if ($values === $this->data) {
            return $this;
        }

        return new static($values);
    }
}
