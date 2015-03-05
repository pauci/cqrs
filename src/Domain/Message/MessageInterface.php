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
     * @return string
     */
    public function getPayloadType();

    /**
     * @return object
     */
    public function getPayload();

    /**
     * @return Metadata
     */
    public function getMetadata();

    /**
     * @param Metadata $metadata
     * @return static
     */
    public function addMetadata(Metadata $metadata);
}
