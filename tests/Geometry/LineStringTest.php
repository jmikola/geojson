<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\LineString;
use GeoJson\Geometry\MultiPoint;
use GeoJson\Tests\BaseGeoJsonTest;
use InvalidArgumentException;

use function is_subclass_of;
use function json_decode;

class LineStringTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new LineString(
            [[1, 1], [2, 2]],
            ... $extraArgs
        );
    }

    public function testIsSubclassOfMultiPoint(): void
    {
        $this->assertTrue(is_subclass_of(LineString::class, MultiPoint::class));
    }

    public function testConstructorShouldRequireAtLeastTwoPositions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('LineString requires at least two positions');

        new LineString([[1, 1]]);
    }

    public function testSerialization(): void
    {
        $coordinates = [[1, 1], [2, 2]];
        $lineString = new LineString($coordinates);

        $expected = [
            'type' => GeoJson::TYPE_LINE_STRING,
            'coordinates' => $coordinates,
        ];

        $this->assertSame(GeoJson::TYPE_LINE_STRING, $lineString->getType());
        $this->assertSame($coordinates, $lineString->getCoordinates());
        $this->assertSame($expected, $lineString->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
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

        $expectedCoordinates = [[1, 1], [2, 2]];

        $this->assertInstanceOf(LineString::class, $lineString);
        $this->assertSame(GeoJson::TYPE_LINE_STRING, $lineString->getType());
        $this->assertSame($expectedCoordinates, $lineString->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }
}
