<?php

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use PHPUnit\Framework\TestCase;

class GeometryTest extends TestCase
{
    public function testIsSubclassOfGeoJson()
    {
        $this->assertTrue(is_subclass_of(Geometry::class, GeoJson::class));
    }
}
