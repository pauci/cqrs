<?php

namespace CQRSTest\Serializer;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\Domain\Payload\AbstractEvent;
use CQRS\Serializer\ReflectionSerializer;
use DateTime;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;
use Rhumsaa\Uuid\Uuid;
use stdClass;

class ReflectionSerializerTest extends PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $serializer = new ReflectionSerializer();

        $event = new SomeEvent([
            'foo'    => 'bar',
            'id'     => Uuid::fromString('bd0a32dd-37f1-42ab-807f-c3c29261a9fe'),
            'time'   => new DateTimeImmutable('2014-08-15 10:12:14'),
            'object' => new stdClass(),
        ]);
        $data = $serializer->serialize($event, 'json');

        $timezone = strtr($event->time->getTimezone()->getName(), ['/' => '\/']);

        $this->assertEquals(
            '{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","foo":"bar","id":{"php_class":"Rhumsaa\\\Uuid\\\Uuid",'
            . '"uuid":"bd0a32dd-37f1-42ab-807f-c3c29261a9fe"},"time":{"php_class":"DateTimeImmutable",'
            . '"time":"2014-08-15 10:12:14.000000","timezone":"'  . $timezone . '"},"object":{"php_class":"stdClass"}}',
            $data
        );
    }

    public function testDeserialize()
    {
        $serializer = new ReflectionSerializer();

        $data = '{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","foo":"bar","id":{"php_class":"Rhumsaa\\\Uuid\\\Uuid",'
            . '"uuid":"d97f7374-b4d9-418a-8dc7-dfda0bcb785a"},"time":{"php_class":"DateTimeImmutable",'
            . '"time":"2014-08-15 10:12:14.020300","timezone":"Europe\\/Bratislava"},"object":{"php_class":"stdClass"}}';

        /** @var SomeEvent $event */
        $event = $serializer->deserialize($data, '', 'json');

        $this->assertInstanceOf(SomeEvent::class, $event);
        $this->assertEquals('bar', $event->foo);
        $this->assertInstanceOf(Uuid::class, $event->id);
        $this->assertEquals('d97f7374-b4d9-418a-8dc7-dfda0bcb785a', (string) $event->id);
        $this->assertEquals('2014-08-15 10:12:14.020300', $event->time->format('Y-m-d H:i:s.u'));
        $this->assertInstanceOf(stdClass::class, $event->object);
    }
}

/**
 * @property-read string $foo
 * @property-read Uuid $id
 * @property-read DateTime $time
 * @property-read stdClass $object
 */
class SomeEvent extends AbstractEvent
{
    protected $foo;
    protected $id;
    protected $time;
    protected $object;
}

class SomeAggregate extends AbstractAggregateRoot
{
    public function getId()
    {
        return $this->getIdReference();
    }

    protected function &getIdReference()
    {
        $id = 4;
        return $id;
    }
}
