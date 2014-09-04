<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $metadata = new Metadata([
            'foo'   => 'bar',
            'first' => 'value',
            'last'  => null
        ]);

        $this->assertSame([
            'first' => 'value',
            'foo'   => 'bar',
            'last'  => null
        ], $metadata->getItems());
    }

    public function testArrayAccess()
    {
        $metadata = new Metadata(['foo' => 'bar']);

        $this->assertTrue(isset($metadata['foo']));
        $this->assertEquals('bar', $metadata['foo']);

        $this->assertFalse(isset($metadata['baz']));
        $this->assertNull($metadata['baz']);
    }

    public function testArraySetForImmutability()
    {
        $metadata = new Metadata(['foo' => 'bar']);

        $this->setExpectedException('CQRS\Exception\RuntimeException', 'Event metadata is immutable.');
        $metadata['foo'] = 'bar';
    }

    public function testArrayUnsetForImmutability()
    {
        $metadata = new Metadata(['foo' => 'bar']);

        $this->setExpectedException('CQRS\Exception\RuntimeException', 'Event metadata is immutable.');
        unset($metadata['foo']);
    }

    public function testMergedWith()
    {
        $metadata = new Metadata(['foo' => 'bar']);

        $mergedMetadata = $metadata->mergedWith(['foo' => 'baz']);
        $this->assertNotSame($metadata, $mergedMetadata);
        $this->assertEquals($mergedMetadata->getItems(), ['foo' => 'baz']);

        $this->assertSame($metadata, $metadata->mergedWith(['foo' => 'bar']));
        $this->assertEquals($metadata->getItems(), ['foo' => 'bar']);
    }
}
