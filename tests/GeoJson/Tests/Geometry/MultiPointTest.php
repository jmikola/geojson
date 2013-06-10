<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\MultiPoint;
use GeoJson\Geometry\Point;

class MultiPointTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\MultiPoint', 'GeoJson\Geometry\Geometry'));
    }

    public function testConstructionFromPointObjects()
    {
        $multiPoint1 = new MultiPoint(array(
            new Point(array(1, 1)),
            new Point(array(2, 2)),
        ));

        $multiPoint2 = new MultiPoint(array(
            array(1, 1),
            array(2, 2),
        ));

        $this->assertSame($multiPoint1->getCoordinates(), $multiPoint2->getCoordinates());
    }

    public function testSerialization()
    {
        $coordinates = array(array(1, 1), array(2, 2));
        $multiPoint = new MultiPoint($coordinates);

        $expected = array(
            'type' => 'MultiPoint',
            'coordinates' => $coordinates,
        );

        $this->assertSame('MultiPoint', $multiPoint->getType());
        $this->assertSame($coordinates, $multiPoint->getCoordinates());
        $this->assertSame($expected, $multiPoint->jsonSerialize());
    }
}
