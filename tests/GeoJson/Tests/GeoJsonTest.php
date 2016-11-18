<?php

namespace GeoJson\Tests;

use GeoJson\GeoJson;

class GeoJsonTest extends \PHPUnit_Framework_TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertInstanceOf('JsonSerializable', $this->getMock('GeoJson\GeoJson'));
    }

    public function testIsJsonUnserializable()
    {
        $this->assertInstanceOf('GeoJson\JsonUnserializable', $this->getMock('GeoJson\GeoJson'));
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

        $this->assertInstanceOf('GeoJson\Geometry\Point', $point);
        $this->assertSame('Point', $point->getType());
        $this->assertSame(array(1, 1), $point->getCoordinates());

        $boundingBox = $point->getBoundingBox();

        $this->assertInstanceOf('GeoJson\BoundingBox', $boundingBox);
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

        $this->assertInstanceOf('GeoJson\Geometry\Point', $point);
        $this->assertSame('Point', $point->getType());
        $this->assertSame(array(1, 1), $point->getCoordinates());

        $crs = $point->getCrs();

        $expectedProperties = array('name' => 'urn:ogc:def:crs:OGC:1.3:CRS84');

        $this->assertInstanceOf('GeoJson\CoordinateReferenceSystem\Named', $crs);
        $this->assertSame('name', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    /**
     * @dataProvider provideInvalidJsonTypes
     */
    public function testJsonUnserializeShouldThrowExceptionWhenJsonIsNotArrayOrOject($invalidType)
    {
        $this->setExpectedException(
            '\GeoJson\Exception\UnserializationException',
            sprintf('GeoJson expected value of type array or object, %s given', gettype($invalidType))
        );

        GeoJson::jsonUnserialize($invalidType);
    }

    /**
     * @expectedException GeoJson\Exception\UnserializationException
     * @expectedExceptionMessage GeoJson expected "type" property of type string, none given
     */
    public function testJsonUnserializeShouldThrowExceptionWhenTypePropertyIsMissing()
    {
        $json = <<<'JSON'
{
}
JSON;

        $json = json_decode($json);
        GeoJson::jsonUnserialize($json);
    }

    /**
     * @expectedException GeoJson\Exception\UnserializationException
     * @expectedExceptionMessage Polygon expected "coordinates" property of type array, none given
     */
    public function testJsonUnserializeShouldThrowExceptionWhenTypeIsPolygonAndCoordinatesPropertyIsMissing()
    {
        $json = <<<'JSON'
{
    "type": "Polygon"
}
JSON;

        $json = json_decode($json);
        GeoJson::jsonUnserialize($json);
    }
////
    /**
     * @dataProvider provideInvalidCoordinates
     */
    public function testJsonUnserializeShouldThrowExceptionWhenTypeIsPolygonAndCoordinatesIsNotArray($invalidType)
    {
        $this->setExpectedException(
            'GeoJson\Exception\UnserializationException',
            sprintf('Polygon expected "coordinates" property of type array, %s given', gettype($invalidType))
        );

        $jsonInvalidType = json_encode($invalidType);

        $json = <<<JSON
{
    "type": "Polygon",
    "coordinates": {$jsonInvalidType}
}
JSON;

        $json = json_decode($json);
        GeoJson::jsonUnserialize($json);
    }

    /**
     * @dataProvider provideInvalidGeometry
     */
    public function testJsonUnserializeShouldThrowExceptionWhenTypeIsFeatureAndGeometryIsSetAndIsNotArrayOrOject(
        $invalidType
    ) {
        $this->setExpectedException(
            'GeoJson\Exception\UnserializationException',
            sprintf('Feature expected "geometry" property of type array or object, %s given', gettype($invalidType))
        );

        $jsonInvalidType = json_encode($invalidType);

        $json = <<<JSON
{
    "type": "Feature",
    "geometry": {$jsonInvalidType}
}
JSON;

        $json = json_decode($json);
        GeoJson::jsonUnserialize($json);
    }

    /**
     * @dataProvider provideInvalidProperties
     */
    public function testJsonUnserializeShouldThrowExceptionWhenTypeIsFeatureAndPropertiesIsSetAndIsNotArrayOrOject(
        $invalidType
    ) {
        $this->setExpectedException(
            'GeoJson\Exception\UnserializationException',
            sprintf('Feature expected "properties" property of type array or object, %s given', gettype($invalidType))
        );

        $jsonInvalidType = json_encode($invalidType);

        $json = <<<JSON
{
    "type": "Feature",
    "properties": {$jsonInvalidType}
}
JSON;

        $json = json_decode($json);
        GeoJson::jsonUnserialize($json);
    }

    /**
     * @expectedException GeoJson\Exception\UnserializationException
     * @expectedExceptionMessage Invalid GeoJson type "UnsupportedType"
     */
    public function testJsonUnserializeShouldThrowExceptionWhenTypeIsUnsupported()
    {
        $json = <<<'JSON'
{
    "type": "UnsupportedType"
}
JSON;

        $json = json_decode($json);
        GeoJson::jsonUnserialize($json);
    }

    public function provideInvalidCoordinates()
    {
        return array(
            array(null),
            array(1),
            array('a'),
            array(1.1),
            array(true),
        );
    }

    public function provideInvalidGeometry()
    {
        return array(
            array(1),
            array('a'),
            array(1.1),
            array(true),
        );
    }

    public function provideInvalidProperties()
    {
        return array(
            array(1),
            array('a'),
            array(1.1),
            array(true),
        );
    }

    public function provideInvalidJsonTypes()
    {
        return array(
            array(null),
            array(1),
            array('a'),
            array(1.1),
            array(true)
        );
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}
