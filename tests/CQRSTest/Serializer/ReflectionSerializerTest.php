<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\ReflectionSerializer;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ReflectionSerializerTest extends PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $event = new SomeEvent([
            'foo'    => 'bar',
            'id'     => Uuid::fromString('bd0a32dd-37f1-42ab-807f-c3c29261a9fe'),
            'time'   => new DateTimeImmutable('2014-08-15 10:12:14.654321', new DateTimeZone('Australia/Sydney')),
            'object' => new stdClass(),
        ]);

        $serializer = new ReflectionSerializer();
        $data = $serializer->serialize($event);

        // Make the test pass on 32bit system
        $data = strtr($data, ['DegradedUuid' => 'Uuid']);

        $this->assertEquals(
            '{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","foo":"bar","id":{"php_class":"Ramsey\\\Uuid\\\Uuid",'
            . '"uuid":"bd0a32dd-37f1-42ab-807f-c3c29261a9fe"},"time":{"php_class":"DateTimeImmutable",'
            . '"time":"2014-08-15T10:12:14.654321+1000"},"object":{"php_class":"stdClass"}}',
            $data
        );
    }

    public function testDeserialize()
    {
        $data = '{"php_class":"CQRSTest\\\Serializer\\\SomeEvent","foo":"bar","id":{"php_class":"Ramsey\\\Uuid\\\Uuid",'
            . '"uuid":"d97f7374-b4d9-418a-8dc7-dfda0bcb785a"},"time":{"php_class":"DateTimeImmutable",'
            . '"time":"2014-08-15T10:12:14.020300+0300"},"object":{"php_class":"stdClass"}}';

        $serializer = new ReflectionSerializer();
        /** @var SomeEvent $event */
        $event = $serializer->deserialize($data, '');

        $this->assertInstanceOf(SomeEvent::class, $event);
        $this->assertEquals('bar', $event->foo);
        $this->assertInstanceOf(Uuid::class, $event->id);
        $this->assertEquals('d97f7374-b4d9-418a-8dc7-dfda0bcb785a', (string) $event->id);
        $this->assertEquals('2014-08-15T10:12:14.020300+0300', $event->time->format('Y-m-d\TH:i:s.uO'));
        $this->assertInstanceOf(stdClass::class, $event->object);
    }

    public function testDeserializeDateTimeWithTimezone()
    {
        $data = '{"php_class":"DateTime","time":"2014-08-15 10:12:14.020300","timezone":"Australia\\/Sydney"}';

        $serializer = new ReflectionSerializer();
        /** @var DateTime $dateTime */
        $dateTime = $serializer->deserialize($data, '');

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('2014-08-15T10:12:14.020300+1000', $dateTime->format('Y-m-d\TH:i:s.uO'));
    }
}
