<?php

namespace CQRSTest\Serializer\Jms;

use CQRS\Domain\Message\Metadata;
use CQRS\Serializer\Jms\MetadataHandler;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Handler\HandlerRegistry;

class MetadataHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()
            ->configureHandlers(function(HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new MetadataHandler());
            })
            ->build();
    }

    public function testSerializeJson()
    {
        $metadata = Metadata::from(['foo' => 'bar', 'baz' => [1, 2, 3]]);
        $json = $this->serializer->serialize($metadata, 'json');

        $this->assertEquals('{"baz":[1,2,3],"foo":"bar"}', $json);
    }

    public function testDeserializeJson()
    {
        $json = '{"foo":"bar","baz":[1,2,3]}';

        /** @var Metadata $metadata */
        $metadata = $this->serializer->deserialize($json, Metadata::class, 'json');

        $this->assertInstanceOf(Metadata::class, $metadata);
        $this->assertEquals(['baz' => [1, 2, 3], 'foo' => 'bar'], $metadata->toArray());
    }
}
