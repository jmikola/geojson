<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\MultiPolygon;
use GeoJson\Geometry\Polygon;

class MultiPolygonTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\MultiPolygon', 'GeoJson\Geometry\Geometry'));
    }

    public function testConstructionFromPolygonObjects()
    {
        $multiPolygon1 = new MultiPolygon(array(
            new Polygon(array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0)))),
            new Polygon(array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1)))),
        ));

        $multiPolygon2 = new MultiPolygon(array(
            array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0))),
            array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1))),
        ));

        $this->assertSame($multiPolygon1->getCoordinates(), $multiPolygon2->getCoordinates());
    }

    public function testSerialization()
    {
        $coordinates = array(
            array(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0))),
            array(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1))),
        );

        $multiPolygon = new MultiPolygon($coordinates);

        $expected = array(
            'type' => 'MultiPolygon',
            'coordinates' => $coordinates,
        );

        $this->assertSame('MultiPolygon', $multiPolygon->getType());
        $this->assertSame($coordinates, $multiPolygon->getCoordinates());
        $this->assertSame($expected, $multiPolygon->jsonSerialize());
    }
}
