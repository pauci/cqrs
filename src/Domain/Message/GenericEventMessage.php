<?php

namespace CQRS\Domain\Message;

use CQRS\Common\MicrosecondsDateTimeFactory;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class GenericEventMessage extends GenericMessage implements EventMessageInterface
{
    /**
     * @var DateTimeInterface
     */
    private $timestamp;

    /**
     * @param object $payload
     * @param Metadata|array|null $metadata
     * @param UuidInterface|null $id
     * @param DateTimeInterface $timestamp
     */
    public function __construct($payload, $metadata = null, UuidInterface $id = null, DateTimeInterface $timestamp = null)
    {
        parent::__construct($payload, $metadata, $id);
        $this->timestamp = $timestamp ?: MicrosecondsDateTimeFactory::createImmutableNow();
    }

    /**
     * @return DateTimeInterface
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
