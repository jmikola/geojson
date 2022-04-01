<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\LineString;
use GeoJson\Geometry\MultiLineString;
use GeoJson\Tests\BaseGeoJsonTest;

use function is_subclass_of;
use function json_decode;

class MultiLineStringTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new MultiLineString([], ... $extraArgs);
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(MultiLineString::class, Geometry::class));
    }

    public function testConstructionFromLineStringObjects(): void
    {
        $multiLineString1 = new MultiLineString([
            new LineString([[1, 1], [2, 2]]),
            new LineString([[3, 3], [4, 4]]),
        ]);

        $multiLineString2 = new MultiLineString([
            [[1, 1], [2, 2]],
            [[3, 3], [4, 4]],
        ]);

        $this->assertSame($multiLineString1->getCoordinates(), $multiLineString2->getCoordinates());
    }

    public function testSerialization(): void
    {
        $coordinates = [
            [[1, 1], [2, 2]],
            [[3, 3], [4, 4]],
        ];

        $multiLineString = new MultiLineString($coordinates);

        $expected = [
            'type' => 'MultiLineString',
            'coordinates' => $coordinates,
        ];

        $this->assertSame('MultiLineString', $multiLineString->getType());
        $this->assertSame($coordinates, $multiLineString->getCoordinates());
        $this->assertSame($expected, $multiLineString->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "MultiLineString",
    "coordinates": [
        [ [1, 1], [2, 2] ],
        [ [3, 3], [4, 4] ]
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $multiLineString = GeoJson::jsonUnserialize($json);

        $expectedCoordinates = [
            [[1, 1], [2, 2]],
            [[3, 3], [4, 4]],
        ];

        $this->assertInstanceOf(MultiLineString::class, $multiLineString);
        $this->assertSame('MultiLineString', $multiLineString->getType());
        $this->assertSame($expectedCoordinates, $multiLineString->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }
}
