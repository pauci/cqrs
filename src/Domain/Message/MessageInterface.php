<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

interface MessageInterface extends JsonSerializable
{
    public function getId(): UuidInterface;

    /**
     * @return class-string
     */
    public function getPayloadType(): string;

    public function getPayload(): object;

    public function getMetadata(): Metadata;

    public function addMetadata(Metadata $metadata): self;
}
