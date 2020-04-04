<?php

declare(strict_types=1);

namespace CQRS\Domain\Message;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

interface MessageInterface extends JsonSerializable
{
    public function getId(): UuidInterface;

    public function getPayloadType(): string;

    /**
     * @return mixed
     */
    public function getPayload();

    public function getMetadata(): Metadata;

    public function addMetadata(Metadata $metadata): self;
}
