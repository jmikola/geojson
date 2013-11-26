<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\CoordinateReferenceSystem\Linked;

class LinkedTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfCoordinateReferenceSystem()
    {
        $this->assertTrue(is_subclass_of(
            'GeoJson\CoordinateReferenceSystem\Linked',
            'GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem'
        ));
    }

    public function testSerialization()
    {
        $crs = new Linked('http://example.com/crs/42', 'proj4');

        $expected = array(
            'type' => 'link',
            'properties' => array(
                'href' => 'http://example.com/crs/42',
                'type' => 'proj4',
            ),
        );

        $this->assertSame('link', $crs->getType());
        $this->assertSame($expected['properties'], $crs->getProperties());
        $this->assertSame($expected, $crs->jsonSerialize());
    }

    public function testSerializationWithoutHrefType()
    {
        $crs = new Linked('http://example.com/crs/42');

        $expected = array(
            'type' => 'link',
            'properties' => array(
                'href' => 'http://example.com/crs/42',
            ),
        );

        $this->assertSame($expected, $crs->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = <<<'JSON'
{
    "type": "link",
    "properties": {
        "href": "http://example.com/crs/42",
        "type": "proj4"
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $crs = CoordinateReferenceSystem::jsonUnserialize($json);

        $expectedProperties = array(
            'href' => 'http://example.com/crs/42',
            'type' => 'proj4',
        );

        $this->assertInstanceOf('GeoJson\CoordinateReferenceSystem\Linked', $crs);
        $this->assertSame('link', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserializationWithoutHrefType($assoc)
    {
        $json = <<<'JSON'
{
    "type": "link",
    "properties": {
        "href": "http://example.com/crs/42"
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $crs = CoordinateReferenceSystem::jsonUnserialize($json);

        $expectedProperties = array('href' => 'http://example.com/crs/42');

        $this->assertInstanceOf('GeoJson\CoordinateReferenceSystem\Linked', $crs);
        $this->assertSame('link', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
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
     * @expectedExceptionMessage Linked CRS expected "properties" property of type array or object
     */
    public function testUnserializationShouldRequirePropertiesArrayOrObject()
    {
        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'link', 'properties' => null));
    }

    /**
     * @expectedException GeoJson\Exception\UnserializationException
     * @expectedExceptionMessage Linked CRS expected "properties.href" property of type string
     */
    public function testUnserializationShouldRequireHrefProperty()
    {
        CoordinateReferenceSystem::jsonUnserialize(array('type' => 'link', 'properties' => array()));
    }
}
