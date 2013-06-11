<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\Named;

class NamedTest extends \PHPUnit_Framework_TestCase
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
}
