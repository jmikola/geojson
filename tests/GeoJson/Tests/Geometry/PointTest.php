<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use InvalidArgumentException;
use stdClass;

class PointTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(... $extraArgs)
    {
        return new Point([1, 1], ... $extraArgs);
    }

    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of(Point::class, Geometry::class));
    }

    public function testConstructorShouldRequireAtLeastTwoElementsInPosition()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Position requires at least two elements');

        new Point(array(1));
    }

    /**
     * @dataProvider providePositionsWithInvalidTypes
     */
    public function testConstructorShouldRequireIntegerOrFloatElementsInPosition()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Position elements must be integers or floats');

        new Point(func_get_args());
    }

    public function providePositionsWithInvalidTypes()
    {
        return array(
            'strings' => array('1.0', '2'),
            'objects' => array(new stdClass(), new stdClass()),
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

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = <<<'JSON'
{
    "type": "Point",
    "coordinates": [1, 1]
}
JSON;

        $json = json_decode($json, $assoc);
        $point = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertSame('Point', $point->getType());
        $this->assertSame(array(1, 1), $point->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}
