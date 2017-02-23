<?php

namespace CQRS\Serializer\Event;

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

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getPayloadType()
    {
        return $this->payloadType;
    }

    /**
     * @return string
     */
    public function getPayloadData()
    {
        return $this->payloadData;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'error' => $this->error,
            'payload_type' => $this->payloadType,
            'payload_data' => $this->payloadData,
        ];
    }
}
