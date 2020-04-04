<?php

declare(strict_types=1);

namespace CQRS\Domain\Payload;

use CQRS\Exception\RuntimeException;

/**
 * Default Implementation for the payload.
 *
 * Convenience AbstractPayload that helps with construction by mapping an array input to properties.
 * If a passed property does not exist on the class an exception is thrown.
 */
abstract class AbstractPayload
{
    /**
     * @param mixed[] $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->assertPropertyExists($key);
            $this->$key = $value;
        }
    }

    /**
     * Direct read access for protected properties
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        $this->assertPropertyExists($name);
        return $this->$name;
    }

    /**
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException(string $name): void
    {
        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on "%s"',
            $name,
            get_class($this)
        ));
    }

    /**
     * @throws RuntimeException
     */
    private function assertPropertyExists(string $name): void
    {
        $vars = get_object_vars($this);

        if (!array_key_exists($name, $vars)) {
            $this->throwPropertyIsNotValidException($name);
        }
    }
}
