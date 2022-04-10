<?php

declare(strict_types=1);

namespace GeoJson\Tests;

use GeoJson\BoundingBox;
use GeoJson\Exception\UnserializationException;
use GeoJson\JsonUnserializable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

use function func_get_args;
use function json_decode;

class BoundingBoxTest extends TestCase
{
    public function testIsJsonSerializable(): void
    {
        $this->assertInstanceOf('JsonSerializable', new BoundingBox([0, 0, 1, 1]));
    }

    public function testIsJsonUnserializable(): void
    {
        $this->assertInstanceOf(JsonUnserializable::class, new BoundingBox([0, 0, 1, 1]));
    }

    public function testConstructorShouldRequireAtLeastFourValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox requires at least four values');

        new BoundingBox([0, 0]);
    }

    public function testConstructorShouldRequireAnEvenNumberOfValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox requires an even number of values');

        new BoundingBox([0, 0, 1, 1, 2]);
    }

    /**
     * @dataProvider provideBoundsWithInvalidTypes
     */
    public function testConstructorShouldRequireIntegerOrFloatValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox values must be integers or floats');
        new BoundingBox(func_get_args());
    }

    public function provideBoundsWithInvalidTypes()
    {
        return [
            'strings' => ['0', '0.0', '1', '1.0'],
            'objects' => [new stdClass(), new stdClass(), new stdClass(), new stdClass()],
            'arrays' => [[], [], [], []],
        ];
    }

    public function testConstructorShouldRequireMinBeforeMaxValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('BoundingBox min values must precede max values');

        new BoundingBox([-90.0, -95.0, -92.5, 90.0]);
    }

    public function testSerialization(): void
    {
        $bounds = [-180.0, -90.0, 0.0, 180.0, 90.0, 100.0];
        $boundingBox = new BoundingBox($bounds);

        $this->assertSame($bounds, $boundingBox->getBounds());
        $this->assertSame($bounds, $boundingBox->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = '[-180.0, -90.0, 180.0, 90.0]';

        $json = json_decode($json, $assoc);
        $boundingBox = BoundingBox::jsonUnserialize($json);

        $this->assertInstanceOf(BoundingBox::class, $boundingBox);
        $this->assertSame([-180.0, -90.0, 180.0, 90.0], $boundingBox->getBounds());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }

    /**
     * @dataProvider provideInvalidUnserializationValues
     */
    public function testUnserializationShouldRequireArray($value): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('BoundingBox expected value of type array');

        BoundingBox::jsonUnserialize($value);
    }

    public function provideInvalidUnserializationValues()
    {
        return [
            [null],
            [1],
            ['foo'],
            [new stdClass()],
        ];
    }
}
