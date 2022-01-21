<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\Exception\UnserializationException;
use GeoJson\JsonUnserializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

class CoordinateReferenceSystemTest extends TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertInstanceOf(
            JsonSerializable::class,
            $this->createMock(CoordinateReferenceSystem::class)
        );
    }

    public function testIsJsonUnserializable()
    {
        $this->assertInstanceOf(
            JsonUnserializable::class,
            $this->createMock(CoordinateReferenceSystem::class)
        );
    }

    public function testUnserializationShouldRequireArrayOrObject()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected value of type array or object');

        CoordinateReferenceSystem::jsonUnserialize(null);
    }

    public function testUnserializationShouldRequireTypeField()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected "type" property of type string, none given');

        CoordinateReferenceSystem::jsonUnserialize(array('properties' => array()));
    }

    public function testUnserializationShouldRequirePropertiesField()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('CRS expected "properties" property of type array or object, none given');

        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'foo'));
    }

    public function testUnserializationShouldRequireValidType()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Invalid CRS type "foo"');

        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'foo', 'properties' => array()));
    }
}
