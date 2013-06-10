<?php

namespace GeoJson\Tests;

class GeoJsonTest extends \PHPUnit_Framework_TestCase
{
    public function testIsJsonSerializable()
    {
        $this->assertTrue(is_subclass_of('GeoJson\GeoJson', 'JsonSerializable'));
    }
}
