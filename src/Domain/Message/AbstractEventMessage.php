<?php

namespace CQRS\Domain\Message;

use CQRS\Util;
use DateTimeImmutable;
use Rhumsaa\Uuid\Uuid;

abstract class AbstractEventMessage extends AbstractMessage implements EventMessageInterface
{
    /** @var DateTimeImmutable */
    private $timestamp;

    /**
     * @param array $payload
     * @param Uuid $id
     * @param Metadata $metadata
     * @param DateTimeImmutable $timestamp
     */
    public function __construct(array $payload = [], Uuid $id = null, Metadata $metadata = null, DateTimeImmutable $timestamp = null)
    {
        parent::__construct($payload, $id, $metadata);
        $this->timestamp = $timestamp ?: Util::createMicrosecondsNow();
    }

    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
