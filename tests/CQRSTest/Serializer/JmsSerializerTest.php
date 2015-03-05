<?php

namespace CQRSTest\Serializer;

use CQRS\Serializer\JmsSerializer;
use JMS\Serializer\Serializer;

class JmsSerializerTest extends \PHPUnit_Framework_TestCase
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

        $jmsSerializer = new JmsSerializer($serializer);
        $this->assertEquals('{}', $jmsSerializer->serialize($event));
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

        $jmsSerializer = new JmsSerializer($serializer);
        $this->assertSame($event, $jmsSerializer->deserialize('{}', 'TestEvent'));
    }
} 
