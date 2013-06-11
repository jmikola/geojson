<?php

namespace GeoJson\Tests\Feature;

use GeoJson\Feature\Feature;
use GeoJson\Tests\BaseGeoJsonTest;

class FeatureTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(array $extraArgs)
    {
        $class = new \ReflectionClass('GeoJson\Feature\Feature');

        return $class->newInstanceArgs(array_merge(array(null, null, null), $extraArgs));
    }

    public function testIsSubclassOfGeoJson()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Feature\Feature', 'GeoJson\GeoJson'));
    }

    public function testSerialization()
    {
        $geometry = $this->getMockGeometry();

        $geometry->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue('geometry'));

        $properties = array('key' => 'value');
        $id = 'identifier';

        $feature = new Feature($geometry, $properties, $id);

        $expected = array(
            'type' => 'Feature',
            'geometry' => 'geometry',
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
            'properties' => new \stdClass(),
        );

        $this->assertEquals($expected, $feature->jsonSerialize());
    }
}
