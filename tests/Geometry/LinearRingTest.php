<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\LinearRing;
use GeoJson\Geometry\LineString;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use InvalidArgumentException;

use function is_subclass_of;

class LinearRingTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new LinearRing(
            [[1, 1], [2, 2], [3, 3], [1, 1]],
            ... $extraArgs
        );
    }

    public function testIsSubclassOfLineString(): void
    {
        $this->assertTrue(is_subclass_of(LinearRing::class, LineString::class));
    }

    public function testConstructorShouldRequireAtLeastFourPositions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('LinearRing requires at least four positions');

        new LinearRing([
            [1, 1],
            [2, 2],
            [3, 3],
        ]);
    }

    public function testConstructorShouldRequireEquivalentFirstAndLastPositions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('LinearRing requires the first and last positions to be equivalent');

        new LinearRing([
            [1, 1],
            [2, 2],
            [3, 3],
            [4, 4],
        ]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorShouldAcceptEquivalentPointObjectsAndPositionArrays(): void
    {
        new LinearRing([
            [1, 1],
            [2, 2],
            [3, 3],
            new Point([1, 1]),
        ]);
    }

    public function testSerialization(): void
    {
        $coordinates = [[1, 1], [2, 2], [3, 3], [1, 1]];
        $linearRing = new LinearRing($coordinates);

        $expected = [
            'type' => GeoJson::TYPE_LINE_STRING,
            'coordinates' => $coordinates,
        ];

        $this->assertSame(GeoJson::TYPE_LINE_STRING, $linearRing->getType());
        $this->assertSame($coordinates, $linearRing->getCoordinates());
        $this->assertSame($expected, $linearRing->jsonSerialize());
    }
}
