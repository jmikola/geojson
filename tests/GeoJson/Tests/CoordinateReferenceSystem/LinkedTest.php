<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\Linked;

class LinkedTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfCoordinateReferenceSystem()
    {
        $this->assertTrue(is_subclass_of(
            'GeoJson\CoordinateReferenceSystem\Linked',
            'GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem'
        ));
    }

    public function testSerialization()
    {
        $crs = new Linked('http://example.com/crs/42', 'proj4');

        $expected = array(
            'type' => 'link',
            'properties' => array(
                'href' => 'http://example.com/crs/42',
                'type' => 'proj4'
            ),
        );

        $this->assertSame('link', $crs->getType());
        $this->assertSame($expected['properties'], $crs->getProperties());
        $this->assertSame($expected, $crs->jsonSerialize());
    }

    public function testSerializationWithNullHrefType()
    {
        $crs = new Linked('http://example.com/crs/42');

        $expected = array(
            'type' => 'link',
            'properties' => array(
                'href' => 'http://example.com/crs/42',
            ),
        );

        $this->assertSame($expected, $crs->jsonSerialize());
    }
}
