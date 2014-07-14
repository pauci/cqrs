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
     * @return array
     */
    public function getPayload();
}
