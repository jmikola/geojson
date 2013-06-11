<?php

namespace GeoJson\CoordinateReferenceSystem;

use GeoJson\Exception\UnserializationException;

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

    /**
     * Factory method for creating a Linked CRS object from properties.
     *
     * @see CoordinateReferenceSystem::jsonUnserialize()
     * @param array|object $properties
     * @return Linked
     * @throws UnserializationException
     */
    protected static function jsonUnserializeFromProperties($properties)
    {
        if ( ! is_array($properties) && ! is_object($properties)) {
            throw UnserializationException::invalidProperty('Linked CRS', 'properties', $properties, 'array or object');
        }

        $properties = new \ArrayObject($properties);

        if ( ! $properties->offsetExists('href')) {
            throw UnserializationException::missingProperty('Linked CRS', 'properties.href', 'string');
        }

        $href = (string) $properties['href'];
        $type = isset($properties['type']) ? (string) $properties['type'] : null;

        return new self($href, $type);
    }
}
