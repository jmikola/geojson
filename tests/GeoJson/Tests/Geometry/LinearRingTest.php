<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\LinearRing;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;

class LinearRingTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass('GeoJson\Geometry\LinearRing');

        return $class->newInstanceArgs(array_merge(
            array(array(array(1, 1), array(2, 2), array(3, 3), array(1, 1))),
            $extraArgs
        ));
    }

    public function testIsSubclassOfLineString()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\LinearRing', 'GeoJson\Geometry\LineString'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage LinearRing requires at least four positions
     */
    public function testConstructorShouldRequireAtLeastFourPositions()
    {
        new LinearRing(array(
            array(1, 1),
            array(2, 2),
            array(3, 3),
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage LinearRing requires the first and last positions to be equivalent
     */
    public function testConstructorShouldRequireEquivalentFirstAndLastPositions()
    {
        new LinearRing(array(
            array(1, 1),
            array(2, 2),
            array(3, 3),
            array(4, 4),
        ));
    }

    public function testConstructorShouldAcceptEquivalentPointObjectsAndPositionArrays()
    {
        new LinearRing(array(
            array(1, 1),
            array(2, 2),
            array(3, 3),
            new Point(array(1, 1)),
        ));
    }

    public function testSerialization()
    {
        $coordinates = array(array(1, 1), array(2, 2), array(3, 3), array(1, 1));
        $linearRing = new LinearRing($coordinates);

        $expected = array(
            'type' => 'LineString',
            'coordinates' => $coordinates,
        );

        $this->assertSame('LineString', $linearRing->getType());
        $this->assertSame($coordinates, $linearRing->getCoordinates());
        $this->assertSame($expected, $linearRing->jsonSerialize());
    }
}
