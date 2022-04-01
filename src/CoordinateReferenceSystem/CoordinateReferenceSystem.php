<?php

declare(strict_types=1);

namespace GeoJson\CoordinateReferenceSystem;

use ArrayObject;
use BadMethodCallException;
use GeoJson\Exception\UnserializationException;
use GeoJson\JsonUnserializable;
use JsonSerializable;

use function is_array;
use function is_object;
use function sprintf;

/**
 * Coordinate reference system object.
 *
 * @deprecated 1.1 Specification of coordinate reference systems has been removed, i.e.,
 *                 the 'crs' member of [GJ2008] is no longer used.
 *
 * @see https://www.rfc-editor.org/rfc/rfc7946#appendix-B.1
 * @see http://www.geojson.org/geojson-spec.html#coordinate-reference-system-objects
 * @since 1.0
 */
abstract class CoordinateReferenceSystem implements JsonSerializable, JsonUnserializable
{
    protected array $properties;

    protected string $type;

    /**
     * Return the properties for this CRS object.
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Return the type for this CRS object.
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'properties' => $this->properties,
        ];
    }

    /**
     * @param array|object $json
     */
    final public static function jsonUnserialize($json): self
    {
        if (! is_array($json) && ! is_object($json)) {
            throw UnserializationException::invalidValue('CRS', $json, 'array or object');
        }

        $json = new ArrayObject($json);

        if (! $json->offsetExists('type')) {
            throw UnserializationException::missingProperty('CRS', 'type', 'string');
        }

        if (! $json->offsetExists('properties')) {
            throw UnserializationException::missingProperty('CRS', 'properties', 'array or object');
        }

        $type = (string) $json['type'];
        $properties = $json['properties'];

        switch ($type) {
            case 'link':
                return Linked::jsonUnserializeFromProperties($properties);

            case 'name':
                return Named::jsonUnserializeFromProperties($properties);
        }

        throw UnserializationException::unsupportedType('CRS', $type);
    }

    /**
     * Factory method for creating a CRS object from properties.
     *
     * This method must be overridden in a child class.
     *
     * @param array|object $properties
     *
     * @throws BadMethodCallException
     */
    protected static function jsonUnserializeFromProperties($properties): CoordinateReferenceSystem
    {
        throw new BadMethodCallException(sprintf('%s must be overridden in a child class', __METHOD__));
    }
}
