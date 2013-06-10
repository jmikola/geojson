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
    }
}
