<?php

namespace GeoJson\Geometry;

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
    protected $type = 'MultiPolygon';

    /**
     * Constructor.
     *
     * @param float[][][][]|Polygon[] $polygons
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $polygons)
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

        if (func_num_args() > 1) {
            $this->setOptionalConstructorArgs(array_slice(func_get_args(), 1));
        }
    }
}
