<?php

declare(strict_types=1);

namespace GeoJson\Tests\Feature;

use GeoJson\Feature\Feature;
use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use stdClass;

use function is_subclass_of;
use function json_decode;

class FeatureTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new Feature(null, null, null, ... $extraArgs);
    }

    public function testIsSubclassOfGeoJson(): void
    {
        $this->assertTrue(is_subclass_of(Feature::class, GeoJson::class));
    }

    public function testSerialization(): void
    {
        $geometry = $this->getMockGeometry();

        $geometry->method('jsonSerialize')->willReturn(['geometry']);

        $properties = ['key' => 'value'];
        $id = 'identifier';

        $feature = new Feature($geometry, $properties, $id);

        $expected = [
            'type' => GeoJson::TYPE_FEATURE,
            'geometry' => ['geometry'],
            'properties' => $properties,
            'id' => 'identifier',
        ];

        $this->assertSame(GeoJson::TYPE_FEATURE, $feature->getType());
        $this->assertSame($geometry, $feature->getGeometry());
        $this->assertSame($id, $feature->getId());
        $this->assertSame($properties, $feature->getProperties());
        $this->assertSame($expected, $feature->jsonSerialize());
    }

    public function testSerializationWithNullConstructorArguments(): void
    {
        $feature = new Feature();

        $expected = [
            'type' => GeoJson::TYPE_FEATURE,
            'geometry' => null,
            'properties' => null,
        ];

        $this->assertSame($expected, $feature->jsonSerialize());
    }

    public function testSerializationShouldConvertEmptyPropertiesArrayToObject(): void
    {
        $feature = new Feature(null, []);

        $expected = [
            'type' => GeoJson::TYPE_FEATURE,
            'geometry' => null,
            'properties' => new stdClass(),
        ];

        $this->assertEquals($expected, $feature->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
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
        $this->assertSame(GeoJson::TYPE_FEATURE, $feature->getType());
        $this->assertSame('test.feature.1', $feature->getId());
        $this->assertSame(['key' => 'value'], $feature->getProperties());

        $geometry = $feature->getGeometry();

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
}
