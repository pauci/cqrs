<?php

namespace CQRS\Domain\Message;

use Rhumsaa\Uuid\Uuid;

class GenericMessage implements MessageInterface
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var object
     */
    private $payload;

    /**
     * @var string
     */
    private $payloadType;

    /**
     * @param object $payload
     * @param Metadata|array $metadata
     * @param Uuid $id
     */
    public function __construct($payload, $metadata = null, Uuid $id = null)
    {
        $this->id          = $id ?: Uuid::uuid4();
        $this->metadata    = Metadata::from($metadata);
        $this->payload     = $payload;
        $this->payloadType = get_class($payload);
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return object
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getPayloadType()
    {
        return $this->payloadType;
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
