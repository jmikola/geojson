<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\LinearRing;
use GeoJson\Geometry\Polygon;
use GeoJson\Tests\BaseGeoJsonTest;

use function is_subclass_of;
use function json_decode;

class PolygonTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new Polygon(
            [
                [[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]],
                [[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]],
            ],
            ... $extraArgs
        );
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(Polygon::class, Geometry::class));
    }

    public function testConstructionFromLinearRingObjects(): void
    {
        $polygon1 = new Polygon([
            new LinearRing([[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]]),
            new LinearRing([[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]]),
        ]);

        $polygon2 = new Polygon([
            [[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]],
            [[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]],
        ]);

        $this->assertSame($polygon1->getCoordinates(), $polygon2->getCoordinates());
    }

    public function testSerialization(): void
    {
        $coordinates = [
            [[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]],
            [[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]],
        ];

        $polygon = new Polygon($coordinates);

        $expected = [
            'type' => 'Polygon',
            'coordinates' => $coordinates,
        ];

        $this->assertSame('Polygon', $polygon->getType());
        $this->assertSame($coordinates, $polygon->getCoordinates());
        $this->assertSame($expected, $polygon->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
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

        $expectedCoordinates = [
            [[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]],
            [[1, 1], [1, 3], [3, 3], [3, 1], [1, 1]],
        ];

        $this->assertInstanceOf(Polygon::class, $polygon);
        $this->assertSame('Polygon', $polygon->getType());
        $this->assertSame($expectedCoordinates, $polygon->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }
}
