<?php

namespace GeoJson\Tests;

use GeoJson\BoundingBox;

class BoundingBoxTest extends \PHPUnit_Framework_TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertTrue(is_subclass_of('GeoJson\BoundingBox', 'JsonSerializable'));
    }

    public function testIsJsonUnserializable()
    {
        $this->assertTrue(is_subclass_of('GeoJson\BoundingBox', 'GeoJson\JsonUnserializable'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BoundingBox requires at least four values
     */
    public function testConstructorShouldRequireAtLeastFourValues()
    {
        new BoundingBox(array(0, 0));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BoundingBox requires an even number of values
     */
    public function testConstructorShouldRequireAnEvenNumberOfValues()
    {
        new BoundingBox(array(0, 0, 1, 1, 2));
    }

    /**
     * @dataProvider provideBoundsWithInvalidTypes
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BoundingBox values must be integers or floats
     */
    public function testConstructorShouldRequireIntegerOrFloatValues()
    {
        new BoundingBox(func_get_args());
    }

    public function provideBoundsWithInvalidTypes()
    {
        return array(
            'strings' => array('0', '0.0', '1', '1.0'),
            'objects' => array(new \stdClass(), new \stdClass(), new \stdClass(), new \stdClass()),
            'arrays' => array(array(), array(), array(), array()),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BoundingBox min values must precede max values
     */
    public function testConstructorShouldRequireMinBeforeMaxValues()
    {
        new BoundingBox(array(1, 1, 0, 0));
    }

    public function testSerialization()
    {
        $bounds = array(-180.0, -90.0, 0.0, 180.0, 90.0, 100.0);
        $boundingBox = new BoundingBox($bounds);

        $this->assertSame($bounds, $boundingBox->getBounds());
        $this->assertSame($bounds, $boundingBox->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc)
    {
        $json = '[-180.0, -90.0, 180.0, 90.0]';

        $json = json_decode($json, $assoc);
        $boundingBox = BoundingBox::jsonUnserialize($json);

        $this->assertInstanceOf('GeoJson\BoundingBox', $boundingBox);
        $this->assertSame(array(-180.0, -90.0, 180.0, 90.0), $boundingBox->getBounds());
    }

    public function testStringRepresentation()
    {
        $boundingBox = new BoundingBox(array(0, 0, 0, 0));

        $this->assertEquals((string) $boundingBox, json_encode($boundingBox->jsonSerialize()));
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }
}
