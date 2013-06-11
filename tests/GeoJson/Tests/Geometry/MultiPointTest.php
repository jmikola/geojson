<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPoint;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;

class MultiPointTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass('GeoJson\Geometry\MultiPoint');

        return $class->newInstanceArgs(array_merge(
            array(array(
                array(1, 1),
                array(2, 2),
            )),
            $extraArgs
        ));
    }

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

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = <<<'JSON'
{
    "type": "MultiPoint",
    "coordinates": [
        [1, 1],
        [2, 2]
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $multiPoint = GeoJson::jsonUnserialize($json);

        $expectedCoordinates = array(array(1, 1), array(2, 2));

        $this->assertInstanceOf('GeoJson\Geometry\MultiPoint', $multiPoint);
        $this->assertSame('MultiPoint', $multiPoint->getType());
        $this->assertSame($expectedCoordinates, $multiPoint->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}
