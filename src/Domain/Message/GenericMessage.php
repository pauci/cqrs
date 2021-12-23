<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GenericMessage implements MessageInterface
{
    private UuidInterface $id;

    /**
     * @var class-string
     */
    private string $payloadType;

    private object $payload;

    private Metadata $metadata;

    public function __construct(object $payload, Metadata|array $metadata = [], UuidInterface $id = null)
    {
        $this->id = $id ?? Uuid::uuid4();
        $this->payloadType = get_class($payload);
        $this->payload = $payload;
        $this->metadata = Metadata::from($metadata);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'payloadType' => $this->payloadType,
            'payload' => $this->payload,
            'metadata' => $this->metadata,
        ];
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPayloadType(): string
    {
        return $this->payloadType;
    }

    public function getPayload(): object
    {
        return $this->payload;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function addMetadata(Metadata $metadata): MessageInterface
    {
        $metadata = $this->metadata->mergedWith($metadata);

        if ($metadata === $this->metadata) {
            return $this;
        }

        $message = clone $this;
        $message->metadata = $metadata;

        return $message;
    }
}
