<?php

namespace CQRS\Domain\Message;

use Ramsey\Uuid\UuidInterface;

class GenericEventMessage extends GenericMessage implements EventMessageInterface
{
    /**
     * @var Timestamp
     */
    private $timestamp;

    /**
     * @param mixed $payload
     * @param Metadata|array|null $metadata
     * @param UuidInterface|null $id
     * @param Timestamp|null $timestamp
     */
    public function __construct($payload, $metadata = null, UuidInterface $id = null, Timestamp $timestamp = null)
    {
        parent::__construct($payload, $metadata, $id);
        $this->timestamp = $timestamp ?: new Timestamp();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['timestamp'] = $this->timestamp;
        return $data;
    }

    /**
     * @return Timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
