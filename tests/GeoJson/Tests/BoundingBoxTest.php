<?php

namespace GeoJson\Tests;

use GeoJson\BoundingBox;
use GeoJson\Exception\UnserializationException;
use GeoJson\JsonUnserializable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class BoundingBoxTest extends TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertInstanceOf('JsonSerializable', new BoundingBox(array(0, 0, 1, 1)));
    }

    public function testIsJsonUnserializable()
    {
        $this->assertInstanceOf(JsonUnserializable::class, new BoundingBox(array(0, 0, 1, 1)));
    }

    public function testConstructorShouldRequireAtLeastFourValues()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox requires at least four values');

        new BoundingBox(array(0, 0));
    }

    public function testConstructorShouldRequireAnEvenNumberOfValues()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox requires an even number of values');

        new BoundingBox(array(0, 0, 1, 1, 2));
    }

    /**
     * @dataProvider provideBoundsWithInvalidTypes
     */
    public function testConstructorShouldRequireIntegerOrFloatValues()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox values must be integers or floats');
        new BoundingBox(func_get_args());
    }

    public function provideBoundsWithInvalidTypes()
    {
        return array(
            'strings' => array('0', '0.0', '1', '1.0'),
            'objects' => array(new stdClass(), new stdClass(), new stdClass(), new stdClass()),
            'arrays' => array(array(), array(), array(), array()),
        );
    }

    public function testConstructorShouldRequireMinBeforeMaxValues()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox min values must precede max values');

        new BoundingBox(array(-90.0, -95.0, -92.5, 90.0));
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

        $this->assertInstanceOf(BoundingBox::class, $boundingBox);
        $this->assertSame(array(-180.0, -90.0, 180.0, 90.0), $boundingBox->getBounds());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return array(
            'assoc=true' => array(true),
            'assoc=false' => array(false),
        );
    }

    /**
     * @dataProvider provideInvalidUnserializationValues
     */
    public function testUnserializationShouldRequireArray($value)
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('BoundingBox expected value of type array');

        BoundingBox::jsonUnserialize($value);
    }

    public function provideInvalidUnserializationValues()
    {
        return array(
            array(null),
            array(1),
            array('foo'),
            array(new stdClass()),
        );
    }
}
