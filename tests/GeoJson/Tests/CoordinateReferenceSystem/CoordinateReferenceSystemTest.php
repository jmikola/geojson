<?php

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

class CoordinateReferenceSystemTest extends \PHPUnit_Framework_TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertTrue(is_subclass_of(
            'GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem',
            'JsonSerializable'
        ));
    }

    public function testIsJsonUnserializable()
    {
        $this->assertTrue(is_subclass_of(
            'GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem',
            'GeoJson\JsonUnserializable'
        ));
    }
}
