<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

interface DomainEventMessageInterface extends EventMessageInterface
{
    public function getAggregateType(): string;

    public function getAggregateId(): mixed;

    public function getSequenceNumber(): int;
}
