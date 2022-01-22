<?php

declare(strict_types=1);

namespace GeoJson\Tests\Geometry;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use PHPUnit\Framework\TestCase;

use function is_subclass_of;

class GeometryTest extends TestCase
{
    public function testIsSubclassOfGeoJson(): void
    {
        $this->assertTrue(is_subclass_of(Geometry::class, GeoJson::class));
    }
}
