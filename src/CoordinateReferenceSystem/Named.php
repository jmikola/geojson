<?php

declare(strict_types=1);

namespace GeoJson\CoordinateReferenceSystem;

use ArrayObject;
use GeoJson\Exception\UnserializationException;

use function is_array;
use function is_object;

/**
 * Named coordinate reference system object.
 *
 * @deprecated 1.1 Specification of coordinate reference systems has been removed, i.e.,
 *                 the 'crs' member of [GJ2008] is no longer used.
 *
 * @see http://www.geojson.org/geojson-spec.html#named-crs
 * @since 1.0
 */
class Named extends CoordinateReferenceSystem
{
    protected string $type = 'name';

    public function __construct(string $name)
    {
        $this->properties = ['name' => $name];
    }

    /**
     * Factory method for creating a Named CRS object from properties.
     *
     * @param array|object $properties
     *
     * @throws UnserializationException
     */
    protected static function jsonUnserializeFromProperties($properties): self
    {
        if (! is_array($properties) && ! is_object($properties)) {
            throw UnserializationException::invalidProperty('Named CRS', 'properties', $properties, 'array or object');
        }

        $properties = new ArrayObject($properties);

        if (! $properties->offsetExists('name')) {
            throw UnserializationException::missingProperty('Named CRS', 'properties.name', 'string');
        }

        $name = (string) $properties['name'];

        return new self($name);
    }
}
