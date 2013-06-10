<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\LineString;

class LineStringTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfMultiPoint()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\LineString', 'GeoJson\Geometry\MultiPoint'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage LineString requires at least two positions
     */
    public function testConstructorShouldRequireAtLeastTwoPositions()
    {
        new LineString(array(array(1, 1)));
    }

    public function testSerialization()
    {
        $coordinates = array(array(1, 1), array(2, 2));
        $lineString = new LineString($coordinates);

        $expected = array(
            'type' => 'LineString',
            'coordinates' => $coordinates,
        );

        $this->assertSame('LineString', $lineString->getType());
        $this->assertSame($coordinates, $lineString->getCoordinates());
        $this->assertSame($expected, $lineString->jsonSerialize());
    }
}
