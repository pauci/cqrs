<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $metadata = Metadata::from([
            'foo'   => 'bar',
            'first' => 'value',
            'last'  => null
        ]);

        $this->assertSame([
            'first' => 'value',
            'foo'   => 'bar',
            'last'  => null
        ], $metadata->toArray());
    }

    public function testArrayAccess()
    {
        $metadata = Metadata::from(['foo' => 'bar']);

        $this->assertTrue(isset($metadata['foo']));
        $this->assertEquals('bar', $metadata['foo']);

        $this->assertFalse(isset($metadata['baz']));
        $this->assertNull($metadata['baz']);
    }

    public function testArraySetForImmutability()
    {
        $metadata = Metadata::from(['foo' => 'bar']);

        $this->setExpectedException('CQRS\Exception\RuntimeException', 'Event metadata is immutable.');
        $metadata['foo'] = 'bar';
    }

    public function testArrayUnsetForImmutability()
    {
        $metadata = Metadata::from(['foo' => 'bar']);

        $this->setExpectedException('CQRS\Exception\RuntimeException', 'Event metadata is immutable.');
        unset($metadata['foo']);
    }

    public function testMergedWith()
    {
        $metadata = Metadata::from(['foo' => 'bar']);

        $mergedMetadata = $metadata->mergedWith(Metadata::from(['foo' => 'baz']));
        $this->assertNotSame($metadata, $mergedMetadata);
        $this->assertEquals($mergedMetadata->toArray(), ['foo' => 'baz']);

        $this->assertSame($metadata, $metadata->mergedWith(Metadata::from(['foo' => 'bar'])));
        $this->assertEquals($metadata->toArray(), ['foo' => 'bar']);
    }
}
