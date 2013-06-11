<?php

namespace GeoJson\Geometry;

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
    protected $type = 'Polygon';

    /**
     * Constructor.
     *
     * @param float[][][]|LinearRing[] $linearRings
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $linearRings)
    {
        $this->coordinates = array_map(
            function($linearRing) {
                if ( ! $linearRing instanceof LinearRing) {
                    $linearRing = new LinearRing($linearRing);
                }

                return $linearRing->getCoordinates();
            },
            $linearRings
        );

        if (func_num_args() > 1) {
            $this->setOptionalConstructorArgs(array_slice(func_get_args(), 1));
        }
    }
}
