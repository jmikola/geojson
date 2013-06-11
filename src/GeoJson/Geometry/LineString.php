<?php

namespace GeoJson\Geometry;

/**
 * LineString geometry object.
 *
 * Coordinates consist of an array of at least two positions.
 *
 * @see http://www.geojson.org/geojson-spec.html#linestring
 * @since 1.0
 */
class LineString extends MultiPoint
{
    protected $type = 'LineString';

    /**
     * Constructor.
     *
     * @param float[][]|Point[] $positions
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $positions)
    {
        if (count($positions) < 2) {
            throw new \InvalidArgumentException('LineString requires at least two positions');
        }

        call_user_func_array(array('parent', '__construct'), func_get_args());
    }
}
