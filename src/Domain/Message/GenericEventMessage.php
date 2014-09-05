<?php

namespace CQRS\Domain\Message;

use CQRS\EventHandling\EventInterface;
use CQRS\Common\MicrosecondsDateTimeFactory;
use DateTimeInterface;
use Rhumsaa\Uuid\Uuid;

class GenericEventMessage extends GenericMessage implements EventMessageInterface
{
    /**
     * @var DateTimeInterface
     */
    private $timestamp;

    /**
     * @param EventInterface $payload
     * @param Metadata|array $metadata
     * @param Uuid $id
     * @param DateTimeInterface $timestamp
     */
    public function __construct(EventInterface $payload, $metadata = null, Uuid $id = null, DateTimeInterface $timestamp = null)
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