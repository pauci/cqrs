<?php

namespace CQRS\Domain\Message;


use CQRS\Exception\RuntimeException;

abstract class AbstractMessage implements MessageInterface
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
     * @param string $name
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException($name)
    {
        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on message "%s"',
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
