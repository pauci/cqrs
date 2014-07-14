<?php

namespace CQRS\Domain\Message;

use CQRS\Domain\AbstractProtectedPropertyReadAccessObject;
use Rhumsaa\Uuid\Uuid;

abstract class AbstractMessage extends AbstractProtectedPropertyReadAccessObject implements MessageInterface
{
    /** @var Uuid */
    private $id;

    /** @var Metadata */
    private $metadata;

    /**
     * @param array $payload
     * @param Uuid $id
     * @param Metadata $metadata
     */
    public function __construct(array $payload = [], Uuid $id = null, Metadata $metadata = null)
    {
        parent::__construct($payload);
        $this->id       = $id ?: Uuid::uuid4();
        $this->metadata = $metadata ?: new Metadata();
    }

    /**
     * @param Metadata $metadata
     * @return $this|AbstractMessage
     */
    public function withMetadata(Metadata $metadata)
    {
        if ($metadata === $this->metadata) {
            return $this;
        }

        $message = clone $this;
        $message->metadata = $metadata;
        return $message;
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
     * @return array
     */
    public function getPayload()
    {
        return $this->extractProperties();
    }
}
