<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\LineString;
use GeoJson\Tests\BaseGeoJsonTest;

class LineStringTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass('GeoJson\Geometry\LineString');

        return $class->newInstanceArgs(array_merge(
            array(array(array(1, 1), array(2, 2))),
            $extraArgs
        ));
    }

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

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = <<<'JSON'
{
    "type": "LineString",
    "coordinates": [
        [1, 1],
        [2, 2]
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $lineString = GeoJson::jsonUnserialize($json);

        $expectedCoordinates = array(array(1, 1), array(2, 2));

        $this->assertInstanceOf('GeoJson\Geometry\LineString', $lineString);
        $this->assertSame('LineString', $lineString->getType());
        $this->assertSame($expectedCoordinates, $lineString->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}
