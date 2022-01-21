<?php

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * MultiPolygon geometry object.
 *
 * Coordinates consist of an array of Polygon coordinates.
 *
 * @see http://www.geojson.org/geojson-spec.html#multipolygon
 * @since 1.0
 */
class MultiPolygon extends Geometry
{
    protected string $type = 'MultiPolygon';

    /**
     * @param array<Polygon|array<LinearRing|array<Point|array<int|float>>>> $polygons
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(array $polygons, ... $args)
    {
        $this->coordinates = array_map(
            function($polygon) {
                if ( ! $polygon instanceof Polygon) {
                    $polygon = new Polygon($polygon);
                }

                return $polygon->getCoordinates();
            },
            $polygons
        );

        $this->setOptionalConstructorArgs($args);
    }
}
