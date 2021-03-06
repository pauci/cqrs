<?php

declare(strict_types=1);

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\Metadata;
use CQRS\EventStore\GuzzleApiEventStore;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GuzzleApiEventStoreTest extends TestCase
{
    private static GuzzleApiEventStore $apiEventStore;

    public static function setUpBeforeClass(): void
    {
        $mock = new MockHandler(
            [
                new Response(200, [], file_get_contents(__DIR__ . '/SomeApiResponse.json')),
                new Response(200, [], file_get_contents(__DIR__ . '/SomeEmptyApiResponse.json')),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        self::$apiEventStore = new GuzzleApiEventStore($client, new SomeSerializer());
    }

    public function testIterateWithData(): void
    {
        $iterator = self::$apiEventStore->iterate();

        $i = 0;
        /** @var EventMessageInterface $event */
        foreach ($iterator as $event) {
            self::assertInstanceOf(SomeEvent::class, $event->getPayload());
            self::assertInstanceOf(Metadata::class, $event->getMetadata());
            $i++;
        }

        self::assertEquals(5, $i);
    }

    public function testIterateWithNoData(): void
    {
        $iterator = self::$apiEventStore->iterate();

        self::assertFalse($iterator->valid());
    }
}
