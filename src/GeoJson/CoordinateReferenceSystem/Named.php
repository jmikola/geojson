<?php

namespace GeoJson\CoordinateReferenceSystem;

/**
 * Named coordinate reference system object.
 *
 * @see http://www.geojson.org/geojson-spec.html#named-crs
 * @since 1.0
 */
class Named extends CoordinateReferenceSystem
{
    protected $type = 'name';

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->properties = array('name' => (string) $name);
    }
}
