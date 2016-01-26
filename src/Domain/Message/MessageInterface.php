<?php

namespace CQRS\Domain\Message;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

interface MessageInterface extends JsonSerializable
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
     * @return mixed
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
