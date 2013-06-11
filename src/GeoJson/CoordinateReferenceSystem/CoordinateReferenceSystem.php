<?php

namespace GeoJson\CoordinateReferenceSystem;

/**
 * Coordinate reference system object.
 *
 * @see http://www.geojson.org/geojson-spec.html#coordinate-reference-system-objects
 * @since 1.0
 */
abstract class CoordinateReferenceSystem implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $properties;

    /**
     * @var string
     */
    protected $type;

    /**
     * Return the properties for this CRS object.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Return the type for this CRS object.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return array(
            'type' => $this->type,
            'properties' => $this->properties,
        );
    }
}
