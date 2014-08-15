<?php

namespace CQRSTest\Serializer\Jms;

use CQRS\Serializer\Jms\RhumsaaUuidHandler;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Annotation as JMS;
use Rhumsaa\Uuid\Uuid;

class RhumsaaUuidHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Serializer */
    private $serializer;

    /** @var Uuid */
    private $uuid;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()
            ->configureHandlers(function(HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new RhumsaaUuidHandler());
            }) //load RhumsaaUuidHandler
            ->build();

        $this->uuid = Uuid::uuid4();
    }

    public function testSerializeJson()
    {
        $subject = new ObjectWithUuid($this->uuid);
        $json = $this->serializer->serialize($subject, 'json');
        $decoded = json_decode($json);

        $this->assertEquals($this->uuid->toString(), $decoded->uuid);

        $serializedUuid = Uuid::fromString($decoded->uuid);

        $this->assertEquals(0, $this->uuid->compareTo($serializedUuid));
        $this->assertTrue($this->uuid->equals($serializedUuid));
    }

    /**
     * @depends testSerializeJson
     */
    public function testDeserializeJson()
    {
        $subject = $this->serializer->deserialize('{"uuid":"ed34c88e-78b0-11e3-9ade-406c8f20ad00"}', ObjectWithUuid::class, 'json');

        $this->assertEquals(0, $subject->getUuid()->compareTo(Uuid::fromString("ed34c88e-78b0-11e3-9ade-406c8f20ad00")));
        $this->assertTrue($subject->getUuid()->equals(Uuid::fromString("ed34c88e-78b0-11e3-9ade-406c8f20ad00")));
    }
}

class ObjectWithUuid
{
    /**
     * @JMS\Type("Rhumsaa\Uuid\Uuid")
     */
    protected $uuid;

    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function setUuid(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}
