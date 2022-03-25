<?php

namespace GeoJson\Tests;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\Named;
use GeoJson\Exception\UnserializationException;
use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use GeoJson\JsonUnserializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

class GeoJsonTest extends TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertInstanceOf(JsonSerializable::class, $this->createMock(GeoJson::class));
    }

    public function testIsJsonUnserializable()
    {
        $this->assertInstanceOf(JsonUnserializable::class, $this->createMock(GeoJson::class));
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserializationWithBoundingBox($assoc)
    {
        $json = <<<'JSON'
{
    "type": "Point",
    "coordinates": [1, 1],
    "bbox": [-180.0, -90.0, 180.0, 90.0]
}
JSON;

        $json = json_decode($json, $assoc);
        $point = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertSame('Point', $point->getType());
        $this->assertSame(array(1, 1), $point->getCoordinates());

        $boundingBox = $point->getBoundingBox();

        $this->assertInstanceOf(BoundingBox::class, $boundingBox);
        $this->assertSame(array(-180.0, -90.0, 180.0, 90.0), $boundingBox->getBounds());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserializationWithCrs($assoc)
    {
        $json = <<<'JSON'
{
    "type": "Point",
    "coordinates": [1, 1],
    "crs": {
        "type": "name",
        "properties": {
            "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
        }
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $point = GeoJson::jsonUnserialize($json);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertSame('Point', $point->getType());
        $this->assertSame(array(1, 1), $point->getCoordinates());

        $crs = $point->getCrs();

        $expectedProperties = array('name' => 'urn:ogc:def:crs:OGC:1.3:CRS84');

        $this->assertInstanceOf(Named::class, $crs);
        $this->assertSame('name', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    public function testUnserializationWithInvalidArgument()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('GeoJson expected value of type array or object, string given');

        GeoJson::jsonUnserialize('must be array or object, but this is a string');
    }

    public function testUnserializationWithUnknownType()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Invalid GeoJson type "Unknown"');

        GeoJson::jsonUnserialize(['type' => 'Unknown']);
    }

    public function testUnserializationWithMissingType()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('GeoJson expected "type" property of type string, none given');

        GeoJson::jsonUnserialize([]);
    }

    /**
     * @dataProvider provideGeoJsonTypesWithCoordinates
     */
    public function testUnserializationWithMissingCoordinates(string $type)
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage($type . ' expected "coordinates" property of type array, none given');

        GeoJson::jsonUnserialize([
            'type' => $type,
        ]);
    }

    /**
     * @dataProvider provideInvalidCoordinates
     *
     * @param mixed $value
     */
    public function testUnserializationWithInvalidCoordinates($value)
    {
        $valueType = is_object($value) ? get_class($value) : gettype($value);

        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Point expected "coordinates" property of type array, '.$valueType.' given');

        GeoJson::jsonUnserialize([
            'type' => 'Point',
            'coordinates' => $value,
        ]);
    }

    public function testFeatureUnserializationWithInvalidGeometry()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Feature expected "geometry" property of type array or object, string given');

        GeoJson::jsonUnserialize([
            'type' => 'Feature',
            'geometry' => 'must be array or object, but this is a string',
        ]);
    }

    public function testFeatureUnserializationWithInvalidProperties()
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Feature expected "properties" property of type array or object, string given');

        GeoJson::jsonUnserialize([
            'type' => 'Feature',
            'properties' => 'must be array or object, but this is a string',
        ]);
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }

    public function provideGeoJsonTypesWithCoordinates()
    {
        return [
            'LineString' =>['LineString'],
            'MultiLineString' => ['MultiLineString'],
            'MultiPoint' => ['MultiPoint'],
            'MultiPolygon' =>['MultiPolygon'],
            'Point' => ['Point'],
            'Polygon' => ['Polygon'],
        ];
    }

    public function provideInvalidCoordinates()
    {
        return [
            'string' => ['1,1'],
            'int' => [1],
            'bool' => [false],
        ];
    }
}
