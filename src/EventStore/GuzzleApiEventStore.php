<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Serializer\SerializerInterface;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Pauci\DateTime\DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use CQRS\Exception;

class GuzzleApiEventStore implements EventStoreInterface
{
    const DEFAULT_LIMIT = 500;

    /** @var Client */
    private $guzzleClient;

    /** @var SerializerInterface */
    private $serializer;

    /** @var int */
    private $requestLimit;

    public function __construct(
        Client $guzzleClient,
        SerializerInterface $serializer,
        $requestLimit = self::DEFAULT_LIMIT
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->serializer = $serializer;
        $this->requestLimit = $requestLimit;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        throw new Exception\BadMethodCallException('Method is not implemented');
    }

    /**
     * @param int|null $offset
     * @param int $limit
     * @return EventMessageInterface[]
     */
    public function read($offset = null, $limit = 10)
    {
        throw new Exception\BadMethodCallException('Method is not implemented');
    }

    /**
     * @param null|UuidInterface $previousEventId
     * @return Generator
     */
    public function iterate(UuidInterface $previousEventId = null)
    {
        $id = $previousEventId ? $previousEventId->toString() : null;

        while (true) {
            $response = $this->getFromApi($id, $this->requestLimit);
            $events = $response ? $response['_embedded']['event'] : null;

            if (!$events) {
                break;
            }

            $lastId = false;
            foreach ($events as $event) {
                $lastId = $event['id'];
                yield $this->fromArray($event);
            }

            if ($response['count'] < $this->requestLimit || !$lastId) {
                break;
            }

            $id = $lastId;
        }
    }

    /**
     * @param array $data
     * @return GenericDomainEventMessage|GenericEventMessage
     */
    public function fromArray(array $data)
    {
        $payload = $this->serializer->deserialize(json_encode($data['payload']), $data['payloadType']);
        /** @var Metadata $metadata */
        $metadata = $this->serializer->deserialize(json_encode($data['metadata']), Metadata::class);
        $id = Uuid::fromString($data['id']);
        $timestamp = DateTime::fromString("{$data['timestamp']}");

        if (array_key_exists('aggregateType', $data)) {
            return new GenericDomainEventMessage(
                $data['aggregateType'],
                $data['aggregateId'],
                0,
                $payload,
                $metadata,
                $id,
                $timestamp
            );
        }

        return new GenericEventMessage($data['payload'], $data['metadata'], $id, $timestamp);
    }

    /**
     * @param string|null $previousEventId
     * @param int $limit
     * @return array
     */
    private function getFromApi($previousEventId, $limit)
    {
        $params = [
            'count' => $limit,
            'previousEventId' => $previousEventId,
        ];

        try {
            $resp = $this->guzzleClient->get('', ['query' => $params]);
        } catch (TransferException $e) {
            throw new Exception\ApiRequestException($e->getMessage(), $e->getCode(), $e);
        }

        return json_decode($resp->getBody(), true);
    }
}
