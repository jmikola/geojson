<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\MultiPolygon;
use GeoJson\Geometry\Polygon;
use GeoJson\Tests\BaseGeoJsonTest;

use function is_subclass_of;
use function json_decode;

class MultiPolygonTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new MultiPolygon([], ... $extraArgs);
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(MultiPolygon::class, Geometry::class));
    }

    public function testConstructionFromPolygonObjects(): void
    {
        $multiPolygon1 = new MultiPolygon([
            new Polygon([[[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]]]),
            new Polygon([[[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]]]),
        ]);

        $multiPolygon2 = new MultiPolygon([
            [[[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]]],
            [[[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]]],
        ]);

        $this->assertSame($multiPolygon1->getCoordinates(), $multiPolygon2->getCoordinates());
    }

    public function testSerialization(): void
    {
        $coordinates = [
            [[[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]]],
            [[[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]]],
        ];

        $multiPolygon = new MultiPolygon($coordinates);

        $expected = [
            'type' => GeoJson::TYPE_MULTI_POLYGON,
            'coordinates' => $coordinates,
        ];

        $this->assertSame(GeoJson::TYPE_MULTI_POLYGON, $multiPolygon->getType());
        $this->assertSame($coordinates, $multiPolygon->getCoordinates());
        $this->assertSame($expected, $multiPolygon->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "MultiPolygon",
    "coordinates": [
        [ [ [0, 0], [0, 4], [4, 4], [4, 0], [0, 0] ] ],
        [ [ [1, 1], [1, 3], [3, 3], [3, 1], [1, 1] ] ]
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $multiPolygon = GeoJson::jsonUnserialize($json);

        $expectedCoordinates = [
            [[[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]]],
            [[[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]]],
        ];

        $this->assertInstanceOf(MultiPolygon::class, $multiPolygon);
        $this->assertSame(GeoJson::TYPE_MULTI_POLYGON, $multiPolygon->getType());
        $this->assertSame($expectedCoordinates, $multiPolygon->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }
}
