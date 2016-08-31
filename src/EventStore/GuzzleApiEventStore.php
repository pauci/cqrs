<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Ramsey\Uuid\UuidInterface;
use CQRS\Exception;

class GuzzleApiEventStore implements EventStoreInterface
{
    const DEFAULT_LIMIT = 100;

    /** @var Client */
    private $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
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
            $response = $this->getFromApi($id, self::DEFAULT_LIMIT);
            $events = $response['_embedded']['event'];

            $lastId = false;
            foreach ($events as $event) {
                $lastId = $event['id'];
                yield $event;
            }

            if ($response['count'] < self::DEFAULT_LIMIT || !$lastId) {
                break;
            }

            $id = $lastId;
        }
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
