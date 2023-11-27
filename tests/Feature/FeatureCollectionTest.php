<?php

declare(strict_types=1);

namespace GeoJson\Tests\Feature;

use GeoJson\Exception\UnserializationException;
use GeoJson\Feature\Feature;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use GeoJson\Tests\BaseGeoJsonTest;
use InvalidArgumentException;
use stdClass;

use function is_subclass_of;
use function iterator_to_array;
use function json_decode;

class FeatureCollectionTest extends BaseGeoJsonTest
{
    public function createSubjectWithExtraArguments(...$extraArgs)
    {
        return new FeatureCollection([], ... $extraArgs);
    }

    public function testIsSubclassOfGeoJson(): void
    {
        $this->assertTrue(is_subclass_of(FeatureCollection::class, GeoJson::class));
    }


    public function testConstructorShouldRequireArrayOfFeatureObjects(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('FeatureCollection may only contain Feature objects');

        new FeatureCollection([new stdClass()]);
    }

    public function testConstructorShouldReindexFeaturesArrayNumerically(): void
    {
        $feature1 = $this->getMockFeature();
        $feature2 = $this->getMockFeature();

        $features = [
            'one' => $feature1,
            'two' => $feature2,
        ];

        $collection = new FeatureCollection($features);

        $this->assertSame([$feature1, $feature2], iterator_to_array($collection));
    }

    public function testIsTraversable(): void
    {
        $features = [
            $this->getMockFeature(),
            $this->getMockFeature(),
        ];

        $collection = new FeatureCollection($features);

        $this->assertInstanceOf('Traversable', $collection);
        $this->assertSame($features, iterator_to_array($collection));
    }

    public function testIsCountable(): void
    {
        $features = [
            $this->getMockFeature(),
            $this->getMockFeature(),
        ];

        $collection = new FeatureCollection($features);

        $this->assertInstanceOf('Countable', $collection);
        $this->assertCount(2, $collection);
    }

    public function testSerialization(): void
    {
        $features = [
            $this->getMockFeature(),
            $this->getMockFeature(),
        ];

        $features[0]->method('jsonSerialize')->willReturn(['feature1']);
        $features[1]->method('jsonSerialize')->willReturn(['feature2']);

        $collection = new FeatureCollection($features);

        $expected = [
            'type' => GeoJson::TYPE_FEATURE_COLLECTION,
            'features' => [['feature1'], ['feature2']],
        ];

        $this->assertSame(GeoJson::TYPE_FEATURE_COLLECTION, $collection->getType());
        $this->assertSame($features, $collection->getFeatures());
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

        $this->assertInstanceOf(FeatureCollection::class, $collection);
        $this->assertSame(GeoJson::TYPE_FEATURE_COLLECTION, $collection->getType());
        $this->assertCount(1, $collection);

        $features = iterator_to_array($collection);
        $feature = $features[0];

        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertSame(GeoJson::TYPE_FEATURE, $feature->getType());
        $this->assertSame('test.feature.1', $feature->getId());
        $this->assertNull($feature->getProperties());

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

    public function testUnserializationShouldRequireFeaturesProperty(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('FeatureCollection expected "features" property of type array, none given');

        GeoJson::jsonUnserialize(['type' => GeoJson::TYPE_FEATURE_COLLECTION]);
    }

    public function testUnserializationShouldRequireFeaturesArray(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('FeatureCollection expected "features" property of type array');

        GeoJson::jsonUnserialize(['type' => GeoJson::TYPE_FEATURE_COLLECTION, 'features' => null]);
    }
}
