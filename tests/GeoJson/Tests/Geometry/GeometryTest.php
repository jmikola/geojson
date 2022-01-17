<?php

namespace GeoJson\Tests\Geometry;

use PHPUnit\Framework\TestCase;

class GeometryTest extends TestCase
{
    public function testIsSubclassOfGeoJson()
    {
        $this->assertTrue(is_subclass_of('GeoJson\Geometry\Geometry', 'GeoJson\GeoJson'));
    }
}
