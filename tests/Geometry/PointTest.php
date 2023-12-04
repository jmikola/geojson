<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\Exception\InvalidArgumentException;
use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use stdClass;

use function func_get_args;
use function is_subclass_of;
use function json_decode;

class PointTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new Point([1, 1], ... $extraArgs);
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(Point::class, Geometry::class));
    }

    public function testConstructorShouldRequireAtLeastTwoElementsInPosition(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Position requires at least two elements');

        new Point([1]);
    }

    /**
     * @dataProvider providePositionsWithInvalidTypes
     */
    public function testConstructorShouldRequireIntegerOrFloatElementsInPosition(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Position elements must be integers or floats');

        new Point(func_get_args());
    }

    public function providePositionsWithInvalidTypes()
    {
        return [
            'strings' => ['1.0', '2'],
            'objects' => [new stdClass(), new stdClass()],
            'arrays' => [[], []],
        ];
    }

    public function testConstructorShouldAllowMoreThanTwoElementsInAPosition(): void
    {
        $point = new Point([1, 2, 3, 4]);

        $this->assertEquals([1, 2, 3, 4], $point->getCoordinates());
    }

    public function testSerialization(): void
    {
        $coordinates = [1, 1];
        $point = new Point($coordinates);

        $expected = [
            'type' => GeoJson::TYPE_POINT,
            'coordinates' => $coordinates,
        ];

        $this->assertSame(GeoJson::TYPE_POINT, $point->getType());
        $this->assertSame($coordinates, $point->getCoordinates());
        $this->assertSame($expected, $point->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
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
        $this->assertSame(GeoJson::TYPE_POINT, $point->getType());
        $this->assertSame([1, 1], $point->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }
}
