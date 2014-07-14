<?php

namespace CQRS\Domain\Message;

use DateTimeImmutable;

interface EventMessageInterface extends MessageInterface
{
    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp();
}
