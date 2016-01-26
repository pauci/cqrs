<?php

namespace CQRS\Domain\Message;

interface EventMessageInterface extends MessageInterface
{
    /**
     * @return Timestamp
     */
    public function getTimestamp();
}
