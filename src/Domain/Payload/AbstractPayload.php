<?php

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
     * @param array $data
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
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $this->assertPropertyExists($name);
        return $this->$name;
    }

    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException($name)
    {
        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on "%s"',
            $name,
            get_class($this)
        ));
    }

    /**
     * @param string $name
     * @throws RuntimeException
     */
    private function assertPropertyExists($name)
    {
        $vars = get_object_vars($this);

        if (!array_key_exists($name, $vars)) {
            $this->throwPropertyIsNotValidException($name);
        }
    }
}
