<?php

namespace GeoJson;

/**
 * Base GeoJson object.
 *
 * @see http://www.geojson.org/geojson-spec.html#geojson-objects
 * @since 1.0
 */
abstract class GeoJson implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Return the type for this GeoJson object.
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
        return array('type' => $this->type);
    }
}
