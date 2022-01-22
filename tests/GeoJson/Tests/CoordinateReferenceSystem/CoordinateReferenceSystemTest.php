<?php

declare(strict_types=1);

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\Exception\UnserializationException;
use GeoJson\JsonUnserializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

class CoordinateReferenceSystemTest extends TestCase
{
    public function testIsJsonSerializable(): void
    {
        $this->assertInstanceOf(
            JsonSerializable::class,
            $this->createMock(CoordinateReferenceSystem::class)
        );
    }

    public function testIsJsonUnserializable(): void
    {
        $this->assertInstanceOf(
            JsonUnserializable::class,
            $this->createMock(CoordinateReferenceSystem::class)
        );
    }

    public function testUnserializationShouldRequireArrayOrObject(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected value of type array or object');

        CoordinateReferenceSystem::jsonUnserialize(null);
    }

    public function testUnserializationShouldRequireTypeField(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected "type" property of type string, none given');

        CoordinateReferenceSystem::jsonUnserialize(['properties' => []]);
    }

    public function testUnserializationShouldRequirePropertiesField(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected "properties" property of type array or object, none given');

        CoordinateReferenceSystem::jsonUnserialize(['type' => 'foo']);
    }

    public function testUnserializationShouldRequireValidType(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Invalid CRS type "foo"');

        CoordinateReferenceSystem::jsonUnserialize(['type' => 'foo', 'properties' => []]);
    }
}
