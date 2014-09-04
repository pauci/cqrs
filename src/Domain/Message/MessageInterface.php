<?php

namespace CQRS\Domain\Message;

use Rhumsaa\Uuid\Uuid;

interface MessageInterface
{
    /**
     * @return Uuid
     */
    public function getId();

    /**
     * @return Metadata
     */
    public function getMetadata();

    /**
     * @return object
     */
    public function getPayload();

    /**
     * @return string
     */
    public function getPayloadType();

    /**
     * @param array $metadata
     * @return static
     */
    public function addMetadata(array $metadata);
}
