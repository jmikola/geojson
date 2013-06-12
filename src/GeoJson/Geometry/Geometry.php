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

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        $json = parent::jsonSerialize();

        if (isset($this->coordinates)) {
            $json['coordinates'] = $this->coordinates;
        }

        return $json;
    }
}
