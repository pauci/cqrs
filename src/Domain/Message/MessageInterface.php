<?php
declare(strict_types=1);

namespace CQRS\Domain\Message;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

interface MessageInterface extends JsonSerializable
{
    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface;

    /**
     * @return string
     */
    public function getPayloadType(): string;

    /**
     * @return mixed
     */
    public function getPayload();

    /**
     * @return Metadata
     */
    public function getMetadata(): Metadata;

    /**
     * @param Metadata $metadata
     * @return MessageInterface
     */
    public function addMetadata(Metadata $metadata): self;
}
