<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

class CoordinateReferenceSystemTest extends \PHPUnit_Framework_TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertInstanceOf(
            'JsonSerializable',
            $this->getMock('GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem')
        );
    }

    public function testIsJsonUnserializable()
    {
        $this->assertInstanceOf(
            'GeoJson\JsonUnserializable',
            $this->getMock('GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem')
        );
    }
}
