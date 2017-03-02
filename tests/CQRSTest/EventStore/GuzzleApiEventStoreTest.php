<?php

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\Metadata;
use CQRS\EventStore\GuzzleApiEventStore;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class GuzzleApiEventStoreTest extends \PHPUnit_Framework_TestCase
{
    /** @var GuzzleApiEventStore */
    private static $apiEventStore;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler(
            [
                new Response(200, [], file_get_contents(__DIR__ . '/SomeApiResponse.json')),
                new Response(200, [], file_get_contents(__DIR__ . '/SomeEmptyApiResponse.json')),
                new Response(200, [], file_get_contents(__DIR__ . '/SomeEmptyApiResponseWithUnsupportedEvents.json')),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        self::$apiEventStore = new GuzzleApiEventStore($client, new SomeSerializer());
    }

    public function testIterateWithData()
    {
        $iterator = self::$apiEventStore->iterate();

        $i = 0;
        /** @var EventMessageInterface $event */
        foreach ($iterator as $event) {
            $this->assertInstanceOf(SomeEvent::class, $event->getPayload());
            $this->assertInstanceOf(Metadata::class, $event->getMetadata());
            $i++;
        }

        $this->assertEquals(5, $i);
    }

    public function testIterateWithNoData()
    {
        $iterator = self::$apiEventStore->iterate();

        $this->assertFalse($iterator->valid());
    }
}
