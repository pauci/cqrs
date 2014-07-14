<?php

namespace CQRS\Domain;

use CQRS\Exception\RuntimeException;

/**
 * Base class providing direct read-only access for protected properties defined in extending classes
 */
abstract class AbstractProtectedPropertyReadAccessObject
{
    /**
     * Hydrates protected and public properties
     *
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
     * Extracts protected and public properties
     *
     * @return array
     */
    protected function extractProperties()
    {
        return get_object_vars($this);
    }

    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException($name)
    {
        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on object "%s"',
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
        if (!property_exists($this, $name)) {
            $this->throwPropertyIsNotValidException($name);
        }
    }
}
