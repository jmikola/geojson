<?php

namespace GeoJson\Tests\Feature;

use GeoJson\Feature\Feature;
use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use stdClass;

class FeatureTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(... $extraArgs)
    {
        return new Feature(null, null, null, ... $extraArgs);
    }

    public function testIsSubclassOfGeoJson()
    {
        $this->assertTrue(is_subclass_of(Feature::class, GeoJson::class));
    }

    public function testSerialization()
    {
        $geometry = $this->getMockGeometry();

        $geometry->method('jsonSerialize')->willReturn(['geometry']);

        $properties = array('key' => 'value');
        $id = 'identifier';

        $feature = new Feature($geometry, $properties, $id);

        $expected = array(
            'type' => 'Feature',
            'geometry' => ['geometry'],
            'properties' => $properties,
            'id' => 'identifier',
        );

        $this->assertSame('Feature', $feature->getType());
        $this->assertSame($geometry, $feature->getGeometry());
        $this->assertSame($id, $feature->getId());
        $this->assertSame($properties, $feature->getProperties());
        $this->assertSame($expected, $feature->jsonSerialize());
    }

    public function testSerializationWithNullConstructorArguments()
    {
        $feature = new Feature();

        $expected = array(
            'type' => 'Feature',
            'geometry' => null,
            'properties' => null,
        );

        $this->assertSame($expected, $feature->jsonSerialize());
    }

    public function testSerializationShouldConvertEmptyPropertiesArrayToObject()
    {
        $feature = new Feature(null, array());

        $expected = array(
            'type' => 'Feature',
            'geometry' => null,
            'properties' => new stdClass(),
        );

        $this->assertEquals($expected, $feature->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = <<<'JSON'
{
    "type": "Feature",
    "id": "test.feature.1",
    "properties": {
        "key": "value"
    },
    "geometry": {
        "type": "Point",
        "coordinates": [1, 1]
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $feature = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertSame('Feature', $feature->getType());
        $this->assertSame('test.feature.1', $feature->getId());
        $this->assertSame(array('key' => 'value'), $feature->getProperties());

        $geometry = $feature->getGeometry();

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
}
