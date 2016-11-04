<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\JsonSerializer;
use JMS\Serializer\Serializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $event = new SomeEvent();

        $serializer = $this->getMock(Serializer::class, [], [], '', false);
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($event, 'json')
            ->willReturn('{}');
        /** @var Serializer $serializer */

        $jsonSerializer = new JsonSerializer($serializer);
        $this->assertEquals('{}', $jsonSerializer->serialize($event));
    }

    public function testDeserialize()
    {
        $event = new SomeEvent();

        $serializer = $this->getMock(Serializer::class, [], [], '', false);
        $serializer->expects($this->once())
            ->method('deserialize')
            ->with('{}', 'TestEvent', 'json')
            ->willReturn($event);
        /** @var Serializer $serializer */

        $jsonSerializer = new JsonSerializer($serializer);
        $this->assertSame($event, $jsonSerializer->deserialize('{}', 'TestEvent'));
    }
} 
