<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\LinearRing;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use InvalidArgumentException;
use GeoJson\Geometry\LineString;

class LinearRingTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass(LinearRing::class);

        return $class->newInstanceArgs(array_merge(
            array(array(array(1, 1), array(2, 2), array(3, 3), array(1, 1))),
            $extraArgs
        ));
    }

    public function testIsSubclassOfLineString()
    {
        $this->assertTrue(is_subclass_of(LinearRing::class, LineString::class));
    }

    public function testConstructorShouldRequireAtLeastFourPositions()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('LinearRing requires at least four positions');

        new LinearRing(array(
            array(1, 1),
            array(2, 2),
            array(3, 3),
        ));
    }

    public function testConstructorShouldRequireEquivalentFirstAndLastPositions()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('LinearRing requires the first and last positions to be equivalent');

        new LinearRing(array(
            array(1, 1),
            array(2, 2),
            array(3, 3),
            array(4, 4),
        ));
    }

    /**
     * @doesNotPerformAssertions
     */
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
