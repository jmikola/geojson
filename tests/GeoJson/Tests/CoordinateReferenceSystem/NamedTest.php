<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\CoordinateReferenceSystem\Named;
use GeoJson\Exception\UnserializationException;
use PHPUnit\Framework\TestCase;

class NamedTest extends TestCase
{
    public function testIsSubclassOfCoordinateReferenceSystem()
    {
        $this->assertTrue(is_subclass_of(
            'GeoJson\CoordinateReferenceSystem\Named',
            'GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem'
        ));
    }

    public function testSerialization()
    {
        $crs = new Named('urn:ogc:def:crs:OGC:1.3:CRS84');

        $expected = array(
            'type' => 'name',
            'properties' => array(
                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
            ),
        );

        $this->assertSame('name', $crs->getType());
        $this->assertSame($expected['properties'], $crs->getProperties());
        $this->assertSame($expected, $crs->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
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

        $expectedProperties = array('name' => 'urn:ogc:def:crs:OGC:1.3:CRS84');

        $this->assertInstanceOf('GeoJson\CoordinateReferenceSystem\Named', $crs);
        $this->assertSame('name', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }

    public function testUnserializationShouldRequirePropertiesArrayOrObject()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Named CRS expected "properties" property of type array or object');

        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'name', 'properties' => null));
    }

    public function testUnserializationShouldRequireNameProperty()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Named CRS expected "properties.name" property of type string');

        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'name', 'properties' => array()));
    }
}
