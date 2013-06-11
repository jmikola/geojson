<?php

namespace GeoJson\Geometry;

/**
 * MultiLineString geometry object.
 *
 * Coordinates consist of an array of LineString coordinates.
 *
 * @see http://www.geojson.org/geojson-spec.html#multilinestring
 * @since 1.0
 */
class MultiLineString extends Geometry
{
    protected $type = 'MultiLineString';

    /**
     * Constructor.
     *
     * @param float[][][]|LineString[] $lineStrings
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $lineStrings)
    {
        $this->coordinates = array_map(
            function($lineString) {
                if ( ! $lineString instanceof LineString) {
                    $lineString = new LineString($lineString);
                }

                return $lineString->getCoordinates();
            },
            $lineStrings
        );

        if (func_num_args() > 1) {
            $this->setOptionalConstructorArgs(array_slice(func_get_args(), 1));
        }
    }
}
