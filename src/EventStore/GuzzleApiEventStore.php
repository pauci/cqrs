<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use Generator;
use Ramsey\Uuid\UuidInterface;
use CQRS\Exception;

class ApiEventStore implements EventStoreInterface
{
    const DEFAULT_LIMIT = 100;

    /** @var string */
    private $url;

    public function __construct($url)
    {
        if (!extension_loaded('curl')) {
            throw new Exception\MissingExtensionException('cURL extension is not loaded');
        }

        if (!$url) {
            throw new Exception\InvalidArgumentException('URL for event store API is empty');
        }

        $this->url = $url;
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

        $requestUrl = $this->url . '?' . http_build_query($params);

        $curl = curl_init($requestUrl);

        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => 1]);

        $resp = curl_exec($curl);

        curl_close($curl);

        return json_decode($resp, true);
    }
}