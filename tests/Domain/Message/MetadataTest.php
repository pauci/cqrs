<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\Metadata;
use PHPUnit\Framework\TestCase;
use CQRS\Exception\RuntimeException;

class MetadataTest extends TestCase
{
    public function testItSortsValuesByKey(): void
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

    public function testJsonSerialize(): void
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ]);

        $json = json_encode($metadata, JSON_THROW_ON_ERROR, 512);
        $this->assertEquals('{"first":"value","foo":"bar","last":null}', $json);
    }

    public function testJsonDeserialize(): void
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

    public function testCount(): void
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ]);

        $this->assertCount(3, $metadata);
    }

    public function testArrayAccess(): void
    {
        $metadata = Metadata::from(['foo' => 'bar']);

        $this->assertTrue(isset($metadata['foo']));
        $this->assertEquals('bar', $metadata['foo']);

        $this->assertFalse(isset($metadata['baz']));
        $this->assertNull($metadata['baz']);
    }

    public function testArraySetForImmutability(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Event metadata is immutable.');

        $metadata = Metadata::from(['foo' => 'bar']);
        $metadata['foo'] = 'bar';
    }

    public function testArrayUnsetForImmutability(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Event metadata is immutable.');

        $metadata = Metadata::from(['foo' => 'bar']);
        unset($metadata['foo']);
    }

    public function testMergedWith(): void
    {
        $metadata = Metadata::from(['foo' => 'bar']);

        $mergedMetadata = $metadata->mergedWith(Metadata::from(['foo' => 'baz']));
        $this->assertNotSame($metadata, $mergedMetadata);
        $this->assertEquals($mergedMetadata->toArray(), ['foo' => 'baz']);

        $this->assertSame($metadata, $metadata->mergedWith(Metadata::from(['foo' => 'bar'])));
        $this->assertEquals($metadata->toArray(), ['foo' => 'bar']);
    }

    public function testWithoutKeys(): void
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

    public function testIterate(): void
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
