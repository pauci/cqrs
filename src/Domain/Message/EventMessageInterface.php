<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use DateTimeImmutable;

interface EventMessageInterface extends MessageInterface
{
    public function getTimestamp(): DateTimeImmutable;
}
