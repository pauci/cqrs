<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

interface DomainEventMessageInterface extends EventMessageInterface
{
    /**
     * @return string
     */
    public function getAggregateType(): string;

    /**
     * @return mixed
     */
    public function getAggregateId();

    /**
     * @return int
     */
    public function getSequenceNumber(): int;
}
