<?php

namespace GeoJson\CoordinateReferenceSystem;

/**
 * Linked coordinate reference system object.
 *
 * @see http://www.geojson.org/geojson-spec.html#linked-crs
 * @since 1.0
 */
class Linked extends CoordinateReferenceSystem
{
    protected $type = 'link';

    /**
     * Constructor.
     *
     * @param string $href
     * @param string $type
     */
    public function __construct($href, $type = null)
    {
        $this->properties = array('href' => (string) $href);

        if (isset($type)) {
            $this->properties['type'] = (string) $type;
        }
    }
}
