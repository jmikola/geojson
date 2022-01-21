<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Exception\UnserializationException;
use GeoJson\GeoJson;
use GeoJson\Geometry\GeometryCollection;
use GeoJson\Tests\BaseGeoJsonTest;
use InvalidArgumentException;
use GeoJson\Geometry\Geometry;
use GeoJson\Geometry\Point;
use ReflectionClass;
use stdClass;

class GeometryCollectionTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new ReflectionClass(GeometryCollection::class);

        return $class->newInstanceArgs(array_merge(array(array()), $extraArgs));
    }

    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of(GeometryCollection::class, Geometry::class));
    }

    public function testConstructorShouldRequireArrayOfGeometryObjects()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('GeometryCollection may only contain Geometry objects');

        new GeometryCollection(array(new stdClass()));
    }

    public function testConstructorShouldReindexGeometriesArrayNumerically()
    {
        $geometry1 = $this->getMockGeometry();
        $geometry2 = $this->getMockGeometry();

        $geometries = array(
            'one' => $geometry1,
            'two' => $geometry2,
        );

        $collection = new GeometryCollection($geometries);
        $this->assertSame(array($geometry1, $geometry2), iterator_to_array($collection));
    }

    public function testIsTraversable()
    {
        $geometries = array(
            $this->getMockGeometry(),
            $this->getMockGeometry(),
        );

        $collection = new GeometryCollection($geometries);

        $this->assertInstanceOf('Traversable', $collection);
        $this->assertSame($geometries, iterator_to_array($collection));
    }

    public function testIsCountable()
    {
        $geometries = array(
            $this->getMockGeometry(),
            $this->getMockGeometry(),
        );

        $collection = new GeometryCollection($geometries);

        $this->assertInstanceOf('Countable', $collection);
        $this->assertCount(2, $collection);
    }

    public function testSerialization()
    {
        $geometries = array(
            $this->getMockGeometry(),
            $this->getMockGeometry(),
        );

        $geometries[0]->method('jsonSerialize')->willReturn(['geometry1']);
        $geometries[1]->method('jsonSerialize')->willReturn(['geometry2']);

        $collection = new GeometryCollection($geometries);

        $expected = array(
            'type' => 'GeometryCollection',
            'geometries' => array(['geometry1'], ['geometry2']),
        );

        $this->assertSame('GeometryCollection', $collection->getType());
        $this->assertSame($geometries, $collection->getGeometries());
        $this->assertSame($expected, $collection->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
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
        $this->assertSame('GeometryCollection', $collection->getType());
        $this->assertCount(1, $collection);

        $geometries = iterator_to_array($collection);
        $geometry = $geometries[0];

        $this->assertInstanceOf(Point::class, $geometry);
        $this->assertSame('Point', $geometry->getType());
        $this->assertSame(array(1, 1), $geometry->getCoordinates());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }

    public function testUnserializationShouldRequireGeometriesProperty()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('GeometryCollection expected "geometries" property of type array, none given');

        GeoJson::jsonUnserialize(array('type' => 'GeometryCollection'));
    }

    public function testUnserializationShouldRequireGeometriesArray()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('GeometryCollection expected "geometries" property of type array');

        GeoJson::jsonUnserialize(array('type' => 'GeometryCollection', 'geometries' => null));
    }
}
