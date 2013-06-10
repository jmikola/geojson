<?php

namespace GeoJson\Tests\Feature;

use GeoJson\Feature\FeatureCollection;

class FeatureCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfGeoJson()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Feature\FeatureCollection', 'GeoJson\GeoJson'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage FeatureCollection may only contain Feature objects
     */
    public function testConstructorShouldRequireArrayOfFeatureObjects()
    {
        new FeatureCollection(array(new \stdClass()));
    }

    public function testConstructorShouldReindexFeaturesArrayNumerically()
    {
        $feature1 = $this->getMockFeature();
        $feature2 = $this->getMockFeature();

        $features = array(
            'one' => $feature1,
            'two' => $feature2,
        );

        $collection = new FeatureCollection($features);

        $this->assertSame(array($feature1, $feature2), iterator_to_array($collection));
    }

    public function testIsTraversable()
    {
        $features = array(
            $this->getMockFeature(),
            $this->getMockFeature(),
        );

        $collection = new FeatureCollection($features);

        $this->assertInstanceOf('Traversable', $collection);
        $this->assertSame($features, iterator_to_array($collection));
    }

    public function testIsCountable()
    {
        $features = array(
            $this->getMockFeature(),
            $this->getMockFeature(),
        );

        $collection = new FeatureCollection($features);

        $this->assertInstanceOf('Countable', $collection);
        $this->assertCount(2, $collection);
    }

    public function testSerialization()
    {
        $features = array(
            $this->getMockFeature(),
            $this->getMockFeature(),
        );

        $features[0]->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue('feature1'));

        $features[1]->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue('feature2'));

        $collection = new FeatureCollection($features);

        $expected = array(
            'type' => 'FeatureCollection',
            'features' => array('feature1', 'feature2'),
        );

        $this->assertSame('FeatureCollection', $collection->getType());
        $this->assertSame($features, $collection->getFeatures());
        $this->assertSame($expected, $collection->jsonSerialize());
    }

    private function getMockFeature()
    {
        return $this->getMockBuilder('GeoJson\Feature\Feature')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
