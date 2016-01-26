<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyInstanceIsSingleton()
    {
        Metadata::resetEmptyInstance();

        $m1 = Metadata::emptyInstance();
        $m2 = Metadata::emptyInstance();

        $this->assertSame($m1, $m2);
    }

    public function testItSortsValuesByKey()
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ]);

        $this->assertSame([
            'first' => 'value',
            'foo' => 'bar',
            'last' => null,
        ], $metadata->toArray());
    }

    public function testJsonSerialize()
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ]);

        $json = json_encode($metadata);
        $this->assertEquals('{"first":"value","foo":"bar","last":null}', $json);
    }

    public function testJsonDeserialize()
    {
        $metadata = Metadata::jsonDeserialize([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ]);

        $this->assertEquals([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ], $metadata->toArray());
    }

    public function testCount()
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ]);

        $this->assertCount(3, $metadata);
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

    public function testWithoutKeys()
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $withoutKeys = $metadata->withoutKeys(['bar']);
        $this->assertNotSame($metadata, $withoutKeys);
        $this->assertEquals($withoutKeys->toArray(), ['foo' => 'bar']);

        $this->assertSame($metadata, $metadata->withoutKeys(['baz']));
        $this->assertEquals($metadata->toArray(), [
            'foo' => 'bar',
            'bar' => 'baz',
        ]);
    }

    public function testSerialize()
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $serialized = serialize($metadata);

        $this->assertEquals('C:28:"CQRS\Domain\Message\Metadata":46:{a:2:{s:3:"bar";s:3:"baz";s:3:"foo";s:3:"bar";}}', $serialized);
    }

    public function testUnserialize()
    {
        /** @var Metadata $metadata */
        $metadata = unserialize('C:28:"CQRS\Domain\Message\Metadata":46:{a:2:{s:3:"bar";s:3:"baz";s:3:"foo";s:3:"bar";}}');

        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'baz',
        ], $metadata->toArray());
    }

    public function testIterate()
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $data = [];
        foreach ($metadata as $key => $value) {
            $data[$key] = $value;
        }

        $this->assertSame([
            'bar' => 'baz',
            'foo' => 'bar',
        ], $data);
    }
}
