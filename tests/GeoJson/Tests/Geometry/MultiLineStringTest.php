<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\LineString;
use GeoJson\Geometry\MultiLineString;

class MultiLineStringTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\MultiLineString', 'GeoJson\Geometry\Geometry'));
    }

    public function testConstructionFromLineStringObjects()
    {
        $multiLineString1 = new MultiLineString(array(
            new LineString(array(array(1, 1), array(2, 2))),
            new LineString(array(array(3, 3), array(4, 4))),
        ));

        $multiLineString2 = new MultiLineString(array(
            array(array(1, 1), array(2, 2)),
            array(array(3, 3), array(4, 4)),
        ));

        $this->assertSame($multiLineString1->getCoordinates(), $multiLineString2->getCoordinates());
    }

    public function testSerialization()
    {
        $coordinates = array(
            array(array(1, 1), array(2, 2)),
            array(array(3, 3), array(4, 4)),
        );

        $multiLineString = new MultiLineString($coordinates);

        $expected = array(
            'type' => 'MultiLineString',
            'coordinates' => $coordinates,
        );

        $this->assertSame('MultiLineString', $multiLineString->getType());
        $this->assertSame($coordinates, $multiLineString->getCoordinates());
        $this->assertSame($expected, $multiLineString->jsonSerialize());
    }
}
