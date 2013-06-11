<?php

namespace GeoJson\Geometry;

/**
 * LinearRing is a special kind of LineString geometry object.
 *
 * Coordinates consist of an array of at least four positions, where the first
 * and last positions are equivalent.
 *
 * @see http://www.geojson.org/geojson-spec.html#linestring
 * @since 1.0
 */
class LinearRing extends LineString
{
    /**
     * Constructor.
     *
     * @param float[][]|Point[] $positions
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $positions)
    {
        if (count($positions) < 4) {
            throw new \InvalidArgumentException('LinearRing requires at least four positions');
        }

        $lastPosition = end($positions);
        $firstPosition = reset($positions);

        $lastPosition = $lastPosition instanceof Point ? $lastPosition->getCoordinates() : $lastPosition;
        $firstPosition = $firstPosition instanceof Point ? $firstPosition->getCoordinates() : $firstPosition;

        if ($lastPosition !== $firstPosition) {
            throw new \InvalidArgumentException('LinearRing requires the first and last positions to be equivalent');
        }

        call_user_func_array(array('parent', '__construct'), func_get_args());
    }
}
