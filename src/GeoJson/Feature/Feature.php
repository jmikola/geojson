<?php

namespace GeoJson\Feature;

use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;

/**
 * Feature object.
 *
 * @see http://www.geojson.org/geojson-spec.html#feature-objects
 * @since 1.0
 */
class Feature extends GeoJson
{
    protected $type = 'Feature';

    /**
     * @var Geometry
     */
    protected $geometry;

    /**
     * Properties are a JSON object, which corresponds to an associative array.
     *
     * @var array
     */
    protected $properties;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * Constructor.
     *
     * @param Geometry $geometry
     * @param array $properties
     * @param mixed $id
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(Geometry $geometry = null, array $properties = null, $id = null)
    {
        $this->geometry = $geometry;
        $this->properties = $properties;
        $this->id = $id;

        if (func_num_args() > 3) {
            $this->setOptionalConstructorArgs(array_slice(func_get_args(), 3));
        }
    }

    /**
     * Return the Geometry object for this Feature object.
     *
     * @return Geometry
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * Return the identifier for this Feature object.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the properties for this Feature object.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        $json = parent::jsonSerialize();

        $json['geometry'] = isset($this->geometry) ? $this->geometry->jsonSerialize() : null;
        $json['properties'] = isset($this->properties) ? $this->properties : null;

        // Ensure empty associative arrays are encoded as JSON objects
        if ($json['properties'] === array()) {
            $json['properties'] = new \stdClass();
        }

        if (isset($this->id)) {
            $json['id'] = $this->id;
        }

        return $json;
    }
}
