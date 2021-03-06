<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use Pauci\DateTime\DateTime;
use Pauci\DateTime\DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class GenericEventMessage extends GenericMessage implements EventMessageInterface
{
    private DateTimeInterface $timestamp;

    /**
     * @param mixed $payload
     * @param Metadata|array|null $metadata
     */
    public function __construct(
        $payload,
        $metadata = null,
        UuidInterface $id = null,
        DateTimeInterface $timestamp = null
    ) {
        parent::__construct($payload, $metadata, $id);
        $this->timestamp = $timestamp ?? DateTime::microsecondsNow();
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['timestamp'] = $this->timestamp;
        return $data;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }
}
