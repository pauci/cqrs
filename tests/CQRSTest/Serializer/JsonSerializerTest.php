<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $event = new SomeEvent();

        $jsonSerializer = new JsonSerializer();
        $this->assertEquals('{}', $jsonSerializer->serialize($event));
    }

    public function testDeserialize()
    {
        $event = new TestEvent();

        $jsonSerializer = new JsonSerializer();
        $this->assertEquals($event, $jsonSerializer->deserialize('{}', 'CQRSTest\Serializer\TestEvent'));
    }
} 
