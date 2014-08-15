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
     * @return array
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
}
