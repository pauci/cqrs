<?php

namespace CQRSTest\EventStore;

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
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        self::$apiEventStore = new GuzzleApiEventStore($client);
    }

    public function testIterateWithData()
    {
        $iterator = self::$apiEventStore->iterate();

        $i = 0;
        foreach ($iterator as $event) {
            $this->assertEquals('test', $event['payload']['test']);
            $i++;
        }

        $this->assertEquals(5, $i);
    }

    public function testIterateWithNoData()
    {
        $iterator = self::$apiEventStore->iterate();

        $i = 0;
        foreach ($iterator as $event) {
            $i++;
        }

        $this->assertEquals(0, $i);
    }
}
