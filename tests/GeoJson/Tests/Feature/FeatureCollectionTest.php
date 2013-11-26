<?php

namespace GeoJson\Tests\Feature;

use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Tests\BaseGeoJsonTest;

class FeatureCollectionTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass('GeoJson\Feature\FeatureCollection');

        return $class->newInstanceArgs(array_merge(array(array()), $extraArgs));
    }

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

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = <<<'JSON'
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "id": "test.feature.1",
            "geometry": {
                "type": "Point",
                "coordinates": [1, 1]
            }
        }
    ]
}
JSON;

        $json = json_decode($json, $assoc);
        $collection = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf('GeoJson\Feature\FeatureCollection', $collection);
        $this->assertSame('FeatureCollection', $collection->getType());
        $this->assertCount(1, $collection);

        $features = iterator_to_array($collection);
        $feature = $features[0];

        $this->assertInstanceOf('GeoJson\Feature\Feature', $feature);
        $this->assertSame('Feature', $feature->getType());
        $this->assertSame('test.feature.1', $feature->getId());
        $this->assertNull($feature->getProperties());

        $geometry = $feature->getGeometry();

        $this->assertInstanceOf('GeoJson\Geometry\Point', $geometry);
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

    /**
     * @expectedException GeoJson\Exception\UnserializationException
     * @expectedExceptionMessage FeatureCollection expected "features" property of type array, none given
     */
    public function testUnserializationShouldRequireFeaturesProperty()
    {
        GeoJson::jsonUnserialize(array('type' => 'FeatureCollection'));
    }

    /**
     * @expectedException GeoJson\Exception\UnserializationException
     * @expectedExceptionMessage FeatureCollection expected "features" property of type array
     */
    public function testUnserializationShouldRequireFeaturesArray()
    {
        GeoJson::jsonUnserialize(array('type' => 'FeatureCollection', 'features' => null));
    }
}
