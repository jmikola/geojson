<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\Point;

class PointTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\Point', 'GeoJson\Geometry\Geometry'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Position requires at least two elements
     */
    public function testConstructorShouldRequireAtLeastTwoElementsInPosition()
    {
        new Point(array(1));
    }

    /**
     * @dataProvider providePositionsWithInvalidTypes
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Position elements must be integers or floats
     */
    public function testConstructorShouldRequireIntegerOrFloatElementsInPosition()
    {
        new Point(func_get_args());
    }

    public function providePositionsWithInvalidTypes()
    {
        return array(
            'strings' => array('1.0', '2'),
            'objects' => array(new \stdClass(), new \stdClass()),
            'arrays' => array(array(), array()),
        );
    }

    public function testConstructorShouldAllowMoreThanTwoElementsInAPosition()
    {
        $point = new Point(array(1, 2, 3, 4));

        $this->assertEquals(array(1, 2, 3, 4), $point->getCoordinates());
    }

    public function testSerialization()
    {
        $coordinates = array(1, 1);
        $point = new Point($coordinates);

        $expected = array(
            'type' => 'Point',
            'coordinates' => $coordinates,
        );

        $this->assertSame('Point', $point->getType());
        $this->assertSame($coordinates, $point->getCoordinates());
        $this->assertSame($expected, $point->jsonSerialize());
    }
}
