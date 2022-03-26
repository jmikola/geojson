<?php

declare(strict_types=1);

namespace GeoJson\CoordinateReferenceSystem;

use ArrayObject;
use GeoJson\Exception\UnserializationException;

use function is_array;
use function is_object;

/**
 * Linked coordinate reference system object.
 *
 * @deprecated 1.1 Specification of coordinate reference systems has been removed, i.e.,
 *                 the 'crs' member of [GJ2008] is no longer used.
 *
 * @see https://www.rfc-editor.org/rfc/rfc7946#appendix-B.1
 * @see http://www.geojson.org/geojson-spec.html#linked-crs
 * @since 1.0
 */
class Linked extends CoordinateReferenceSystem
{
    protected string $type = 'link';

    public function __construct(string $href, ?string $type = null)
    {
        $this->properties = ['href' => $href];

        if ($type !== null) {
            $this->properties['type'] = $type;
        }
    }

    /**
     * Factory method for creating a Linked CRS object from properties.
     *
     * @param array|object $properties
     *
     * @throws UnserializationException
     */
    protected static function jsonUnserializeFromProperties($properties): self
    {
        if (! is_array($properties) && ! is_object($properties)) {
            throw UnserializationException::invalidProperty('Linked CRS', 'properties', $properties, 'array or object');
        }

        $properties = new ArrayObject($properties);

        if (! $properties->offsetExists('href')) {
            throw UnserializationException::missingProperty('Linked CRS', 'properties.href', 'string');
        }

        $href = (string) $properties['href'];
        $type = isset($properties['type']) ? (string) $properties['type'] : null;

        return new self($href, $type);
    }
}
