<?php

namespace CQRS\Domain\Message;

use Rhumsaa\Uuid\Uuid;

class GenericMessage implements MessageInterface
{
    /** @var Uuid */
    private $id;

    /** @var array */
    private $metadata;

    /** @var object */
    private $payload;

    /** @var string */
    private $payloadType;

    /**
     * @param object $payload
     * @param array $metadata
     * @param Uuid|null $id
     */
    public function __construct($payload, array $metadata = [], Uuid $id = null)
    {
        $this->id          = $id ?: Uuid::uuid4();
        $this->metadata    = $metadata;
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
     * @return array
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
}
