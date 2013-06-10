<?php

namespace GeoJson\Tests\Geometry;

class GeometryTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubclassOfGeoJson()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\Geometry', 'GeoJson\GeoJson'));
    }
}
