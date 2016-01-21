<?php

namespace CQRS\Domain\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GenericMessage implements MessageInterface
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $payloadType;

    /**
     * @var object
     */
    private $payload;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @param object $payload
     * @param Metadata|array $metadata
     * @param UuidInterface|null $id
     */
    public function __construct($payload, $metadata = null, UuidInterface $id = null)
    {
        $this->id          = $id ?: Uuid::uuid4();
        $this->payloadType = get_class($payload);
        $this->payload     = $payload;
        $this->metadata    = Metadata::from($metadata);
    }

    /**
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPayloadType()
    {
        return $this->payloadType;
    }

    /**
     * @return object
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Metadata $metadata
     * @return static
     */
    public function addMetadata(Metadata $metadata)
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
