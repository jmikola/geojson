<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\Exception\InvalidArgumentException;
use GeoJson\Exception\UnserializationException;
use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\GeometryCollection;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use stdClass;

use function is_subclass_of;
use function iterator_to_array;
use function json_decode;

class GeometryCollectionTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new GeometryCollection([], ... $extraArgs);
    }

    public function testIsSubclassOfGeometry(): void
    {
        $this->assertTrue(is_subclass_of(GeometryCollection::class, Geometry::class));
    }

    public function testConstructorShouldRequireArrayOfGeometryObjects(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('GeometryCollection may only contain Geometry objects');

        new GeometryCollection([new stdClass()]);
    }

    public function testConstructorShouldReindexGeometriesArrayNumerically(): void
    {
        $geometry1 = $this->getMockGeometry();
        $geometry2 = $this->getMockGeometry();

        $geometries = [
            'one' => $geometry1,
            'two' => $geometry2,
        ];

        $collection = new GeometryCollection($geometries);
        $this->assertSame([$geometry1, $geometry2], iterator_to_array($collection));
    }

    public function testIsTraversable(): void
    {
        $geometries = [
            $this->getMockGeometry(),
            $this->getMockGeometry(),
        ];

        $collection = new GeometryCollection($geometries);

        $this->assertInstanceOf('Traversable', $collection);
        $this->assertSame($geometries, iterator_to_array($collection));
    }

    public function testIsCountable(): void
    {
        $geometries = [
            $this->getMockGeometry(),
            $this->getMockGeometry(),
        ];

        $collection = new GeometryCollection($geometries);

        $this->assertInstanceOf('Countable', $collection);
        $this->assertCount(2, $collection);
    }

    public function testSerialization(): void
    {
        $geometries = [
            $this->getMockGeometry(),
            $this->getMockGeometry(),
        ];

        $geometries[0]->method('jsonSerialize')->willReturn(['geometry1']);
        $geometries[1]->method('jsonSerialize')->willReturn(['geometry2']);

        $collection = new GeometryCollection($geometries);

        $expected = [
            'type' => GeoJson::TYPE_GEOMETRY_COLLECTION,
            'geometries' => [['geometry1'], ['geometry2']],
        ];

        $this->assertSame(GeoJson::TYPE_GEOMETRY_COLLECTION, $collection->getType());
        $this->assertSame($geometries, $collection->getGeometries());
        $this->assertSame($expected, $collection->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "GeometryCollection",
    "geometries": [
        {
            "type": "Point",
            "coordinates": [1, 1]
        }
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $collection = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(GeometryCollection::class, $collection);
        $this->assertSame(GeoJson::TYPE_GEOMETRY_COLLECTION, $collection->getType());
        $this->assertCount(1, $collection);

        $geometries = iterator_to_array($collection);
        $geometry = $geometries[0];

        $this->assertInstanceOf(Point::class, $geometry);
        $this->assertSame(GeoJson::TYPE_POINT, $geometry->getType());
        $this->assertSame([1, 1], $geometry->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }

    public function testUnserializationShouldRequireGeometriesProperty(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('GeometryCollection expected "geometries" property of type array, none given');

        GeoJson::jsonUnserialize(['type' => GeoJson::TYPE_GEOMETRY_COLLECTION]);
    }

    public function testUnserializationShouldRequireGeometriesArray(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('GeometryCollection expected "geometries" property of type array');

        GeoJson::jsonUnserialize(['type' => GeoJson::TYPE_GEOMETRY_COLLECTION, 'geometries' => null]);
    }
}
