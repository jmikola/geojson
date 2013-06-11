<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\LinearRing;
use GeoJson\Geometry\Polygon;
use GeoJson\Tests\BaseGeoJsonTest;

class PolygonTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass('GeoJson\Geometry\Polygon');

        return $class->newInstanceArgs(array_merge(
            array(array(
                array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0)),
                array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1)),
            )),
            $extraArgs
        ));
    }

    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\Polygon', 'GeoJson\Geometry\Geometry'));
    }

    public function testConstructionFromLinearRingObjects()
    {
        $polygon1 = new Polygon(array(
            new LinearRing(array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0))),
            new LinearRing(array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1))),
        ));

        $polygon2 = new Polygon(array(
            array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0)),
            array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1)),
        ));

        $this->assertSame($polygon1->getCoordinates(), $polygon2->getCoordinates());
    }

    public function testSerialization()
    {
        $coordinates = array(
            array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0)),
            array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1)),
        );

        $polygon = new Polygon($coordinates);

        $expected = array(
            'type' => 'Polygon',
            'coordinates' => $coordinates,
        );

        $this->assertSame('Polygon', $polygon->getType());
        $this->assertSame($coordinates, $polygon->getCoordinates());
        $this->assertSame($expected, $polygon->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = <<<'JSON'
{
    "type": "Polygon",
    "coordinates": [
        [ [0, 0], [0, 4], [4, 4], [4, 0], [0, 0] ],
        [ [1, 1], [1, 3], [3, 3], [3, 1], [1, 1] ]
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $polygon = GeoJson::jsonUnserialize($json);

        $expectedCoordinates = array(
            array(array(0, 0), array(0, 4), array(4, 4), array(4, 0), array(0, 0)),
            array(array(1, 1), array(1, 3), array(3, 3), array(3, 1), array(1, 1)),
        );

        $this->assertInstanceOf('GeoJson\Geometry\Polygon', $polygon);
        $this->assertSame('Polygon', $polygon->getType());
        $this->assertSame($expectedCoordinates, $polygon->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}
