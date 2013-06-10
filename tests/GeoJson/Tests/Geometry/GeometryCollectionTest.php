<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\Geometry\GeometryCollection;

class GeometryCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfGeometry()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\GeometryCollection', 'GeoJson\Geometry\Geometry'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage GeometryCollection may only contain Geometry objects
     */
    public function testConstructorShouldRequireArrayOfGeometryObjects()
    {
        new GeometryCollection(array(new \stdClass()));
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

        $geometries[0]->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue('geometry1'));

        $geometries[1]->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue('geometry2'));

        $collection = new GeometryCollection($geometries);

        $expected = array(
            'type' => 'GeometryCollection',
            'geometries' => array('geometry1', 'geometry2'),
        );

        $this->assertSame('GeometryCollection', $collection->getType());
        $this->assertSame($geometries, $collection->getGeometries());
        $this->assertSame($expected, $collection->jsonSerialize());
    }

    private function getMockGeometry()
    {
        return $this->getMockBuilder('GeoJson\Geometry\Geometry')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
