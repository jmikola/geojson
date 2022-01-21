<?php

namespace GeoJson\Geometry;

use GeoJson\GeoJson;

/**
 * Base geometry object.
 *
 * @see http://www.geojson.org/geojson-spec.html#geometry-objects
 * @since 1.0
 */
abstract class Geometry extends GeoJson
{
    /**
     * @var array
     */
    protected $coordinates;

    /**
     * Return the coordinates for this Geometry object.
     *
     * @return array
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();

        if (isset($this->coordinates)) {
            $json['coordinates'] = $this->coordinates;
        }

        return $json;
    }
}
