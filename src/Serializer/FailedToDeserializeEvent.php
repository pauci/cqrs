<?php

namespace CQRS\Serializer;

use JsonSerializable;

final class FailedToDeserializeEvent implements JsonSerializable
{
    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    private $payloadType;

    /**
     * @var string
     */
    private $payloadData;

    public function __construct(string $error, string $payloadType, string $payloadData)
    {
        $this->error = $error;
        $this->payloadType = $payloadType;
        $this->payloadData = $payloadData;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getPayloadType(): string
    {
        return $this->payloadType;
    }

    public function getPayloadData(): string
    {
        return $this->payloadData;
    }

    public function jsonSerialize(): array
    {
        return [
            'error' => $this->error,
            'payload_type' => $this->payloadType,
            'payload_data' => $this->payloadData,
        ];
    }
}
