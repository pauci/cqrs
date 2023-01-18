<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\UuidInterface;

class GenericEventMessage extends GenericMessage implements EventMessageInterface
{
    private DateTimeImmutable $timestamp;

    private static ClockInterface|null $clock = null;

    public static function setClock(ClockInterface $clock): void
    {
        self::$clock = $clock;
    }

    public function __construct(
        object $payload,
        Metadata|array $metadata = [],
        UuidInterface $id = null,
        DateTimeImmutable $timestamp = null
    ) {
        parent::__construct($payload, $metadata, $id);
        $this->timestamp = $timestamp ?? self::$clock?->now() ?? new DateTimeImmutable();
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['timestamp'] = $this->timestamp;

        return $data;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
