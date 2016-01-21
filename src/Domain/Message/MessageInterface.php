<?php

namespace CQRS\Domain\Message;

use Ramsey\Uuid\UuidInterface;

interface MessageInterface
{
    /**
     * @return UuidInterface
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
