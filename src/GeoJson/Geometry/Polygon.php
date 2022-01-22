<?php

declare(strict_types=1);

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * Polygon geometry object.
 *
 * Coordinates consist of an array of LinearRing coordinates.
 *
 * @see http://www.geojson.org/geojson-spec.html#polygon
 * @since 1.0
 */
class Polygon extends Geometry
{
    protected string $type = 'Polygon';

    /**
     * @param array<LinearRing|array<Point|array<int|float>>> $linearRings
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(array $linearRings, ...$args)
    {
        foreach ($linearRings as $linearRing) {
            if (! $linearRing instanceof LinearRing) {
                $linearRing = new LinearRing($linearRing);
            }
            $this->coordinates[] = $linearRing->getCoordinates();
        }

        $this->setOptionalConstructorArgs($args);
    }
}
