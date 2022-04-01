<?php

declare(strict_types=1);

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\CoordinateReferenceSystem\Named;
use GeoJson\Exception\UnserializationException;
use PHPUnit\Framework\TestCase;

use function is_subclass_of;
use function json_decode;

class NamedTest extends TestCase
{
    public function testIsSubclassOfCoordinateReferenceSystem(): void
    {
        $this->assertTrue(is_subclass_of(Named::class, CoordinateReferenceSystem::class));
    }

    public function testSerialization(): void
    {
        $crs = new Named('urn:ogc:def:crs:OGC:1.3:CRS84');

        $expected = [
            'type' => 'name',
            'properties' => [
                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
            ],
        ];

        $this->assertSame('name', $crs->getType());
        $this->assertSame($expected['properties'], $crs->getProperties());
        $this->assertSame($expected, $crs->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "name",
    "properties": {
        "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $crs = CoordinateReferenceSystem::jsonUnserialize($json);

        $expectedProperties = ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'];

        $this->assertInstanceOf(Named::class, $crs);
        $this->assertSame('name', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }

    public function testUnserializationShouldRequirePropertiesArrayOrObject(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Named CRS expected "properties" property of type array or object');

        CoordinateReferenceSystem::jsonUnserialize(['type' => 'name', 'properties' => null]);
    }

    public function testUnserializationShouldRequireNameProperty(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Named CRS expected "properties.name" property of type string');

        CoordinateReferenceSystem::jsonUnserialize(['type' => 'name', 'properties' => []]);
    }
}
