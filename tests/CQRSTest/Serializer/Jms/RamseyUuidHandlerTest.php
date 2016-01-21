<?php

namespace CQRSTest\Serializer\Jms;

use CQRS\Serializer\Jms\RamseyUuidHandler;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Handler\HandlerRegistry;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class RamseyUuidHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->serializer = SerializerBuilder::create()
            ->configureHandlers(function(HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new RamseyUuidHandler());
            })
            ->build();
    }

    public function testSerializeJson()
    {
        $uuid   = Uuid::fromString('34ca79b8-6181-4b93-903a-ac658e0c5c35');
        $object = new ObjectWithUuid($uuid);

        $json = $this->serializer->serialize($object, 'json');

        $this->assertEquals('{"uuid":"34ca79b8-6181-4b93-903a-ac658e0c5c35"}', $json);
    }

    public function testDeserializeJson()
    {
        $json = '{"uuid":"ed34c88e-78b0-11e3-9ade-406c8f20ad00"}';

        /** @var ObjectWithUuid $object */
        $object = $this->serializer->deserialize($json, ObjectWithUuid::class, 'json');
        $uuid   = $object->getUuid();

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertEquals('ed34c88e-78b0-11e3-9ade-406c8f20ad00', (string) $uuid);
    }
}
