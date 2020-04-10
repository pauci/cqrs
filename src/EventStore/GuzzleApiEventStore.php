<?php

declare(strict_types=1);

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
    public const DEFAULT_LIMIT = 500;

    private Client $guzzleClient;

    private SerializerInterface $serializer;

    private int $requestLimit;

    public function __construct(
        Client $guzzleClient,
        SerializerInterface $serializer,
        int $requestLimit = self::DEFAULT_LIMIT
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->serializer = $serializer;
        $this->requestLimit = $requestLimit;
    }

    public function store(EventMessageInterface $event): void
    {
        throw new Exception\BadMethodCallException('Method is not implemented');
    }

    public function read(int $offset = 0, int $limit = 10): array
    {
        throw new Exception\BadMethodCallException('Method is not implemented');
    }

    /**
     * @return Generator<EventMessageInterface>
     */
    public function iterate(UuidInterface $previousEventId = null): Generator
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
    public function fromArray(array $data): GenericEventMessage
    {
        $payload = $this->serializer->deserialize(
            json_encode($data['payload'], JSON_THROW_ON_ERROR, 512),
            $data['payloadType']
        );

        /** @var Metadata $metadata */
        $metadata = $this->serializer->deserialize(
            json_encode($data['metadata'], JSON_THROW_ON_ERROR, 512),
            Metadata::class
        );

        $id = Uuid::fromString($data['id']);
        $timestamp = DateTime::fromString((string) $data['timestamp']);

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

    private function getFromApi(?string $previousEventId, int $limit): array
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

        return json_decode((string) $resp->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
