<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\MultiPoint;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;

use function is_subclass_of;
use function json_decode;

class MultiPointTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new MultiPoint([], ... $extraArgs);
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(MultiPoint::class, Geometry::class));
    }

    public function testConstructionFromPointObjects(): void
    {
        $multiPoint1 = new MultiPoint([
            new Point([1, 1]),
            new Point([2, 2]),
        ]);

        $multiPoint2 = new MultiPoint([
            [1, 1],
            [2, 2],
        ]);

        $this->assertSame($multiPoint1->getCoordinates(), $multiPoint2->getCoordinates());
    }

    public function testSerialization(): void
    {
        $coordinates = [[1, 1], [2, 2]];
        $multiPoint = new MultiPoint($coordinates);

        $expected = [
            'type' => 'MultiPoint',
            'coordinates' => $coordinates,
        ];

        $this->assertSame('MultiPoint', $multiPoint->getType());
        $this->assertSame($coordinates, $multiPoint->getCoordinates());
        $this->assertSame($expected, $multiPoint->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
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

        $expectedCoordinates = [[1, 1], [2, 2]];

        $this->assertInstanceOf(MultiPoint::class, $multiPoint);
        $this->assertSame('MultiPoint', $multiPoint->getType());
        $this->assertSame($expectedCoordinates, $multiPoint->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }
}
