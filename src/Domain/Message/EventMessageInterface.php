<?php

namespace CQRS\Domain\Message;

use DateTimeInterface;

interface EventMessageInterface extends MessageInterface
{
    /**
     * @return DateTimeInterface
     */
    public function getTimestamp();
}
