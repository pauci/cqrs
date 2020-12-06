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

        self::assertSame([
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
        self::assertEquals('{"first":"value","foo":"bar","last":null}', $json);
    }

    public function testJsonDeserialize(): void
    {
        $metadata = Metadata::jsonDeserialize([
            'foo' => 'bar',
            'first' => 'value',
            'last' => null,
        ]);

        self::assertEquals([
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

        self::assertCount(3, $metadata);
    }

    public function testArrayAccess(): void
    {
        $metadata = Metadata::from(['foo' => 'bar']);

        self::assertTrue(isset($metadata['foo']));
        self::assertEquals('bar', $metadata['foo']);

        self::assertFalse(isset($metadata['baz']));
        self::assertNull($metadata['baz']);
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
        self::assertNotSame($metadata, $mergedMetadata);
        self::assertEquals($mergedMetadata->toArray(), ['foo' => 'baz']);

        self::assertSame($metadata, $metadata->mergedWith(Metadata::from(['foo' => 'bar'])));
        self::assertEquals($metadata->toArray(), ['foo' => 'bar']);
    }

    public function testWithoutKeys(): void
    {
        $metadata = Metadata::from([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $withoutKeys = $metadata->withoutKeys(['bar']);
        self::assertNotSame($metadata, $withoutKeys);
        self::assertEquals($withoutKeys->toArray(), ['foo' => 'bar']);

        self::assertSame($metadata, $metadata->withoutKeys(['baz']));
        self::assertEquals($metadata->toArray(), [
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

        self::assertSame([
            'bar' => 'baz',
            'foo' => 'bar',
        ], $data);
    }
}
