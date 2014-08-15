<?php

namespace CQRS\Domain\Message;

interface DomainEventMessageInterface extends EventMessageInterface
{
    /**
     * @return string
     */
    public function getAggregateType();

    /**
     * @return mixed
     */
    public function getAggregateId();

    /**
     * @return int
     */
    public function getSequenceNumber();
}
