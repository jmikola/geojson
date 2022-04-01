<?php

declare(strict_types=1);

namespace GeoJson;

use ArrayObject;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\Exception\UnserializationException;
use JsonSerializable;

use function array_map;
use function is_array;
use function is_object;
use function sprintf;
use function strncmp;

/**
 * Base GeoJson object.
 *
 * @see http://www.geojson.org/geojson-spec.html#geojson-objects
 * @since 1.0
 */
abstract class GeoJson implements JsonSerializable, JsonUnserializable
{
    protected ?BoundingBox $boundingBox = null;

    protected ?CoordinateReferenceSystem $crs = null;

    protected string $type;

    /**
     * Return the BoundingBox for this GeoJson object.
     */
    public function getBoundingBox(): ?BoundingBox
    {
        return $this->boundingBox;
    }

    /**
     * Return the CoordinateReferenceSystem for this GeoJson object.
     */
    public function getCrs(): ?CoordinateReferenceSystem
    {
        return $this->crs;
    }

    /**
     * Return the type for this GeoJson object.
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        $json = ['type' => $this->type];

        if (isset($this->crs)) {
            $json['crs'] = $this->crs->jsonSerialize();
        }

        if (isset($this->boundingBox)) {
            $json['bbox'] = $this->boundingBox->jsonSerialize();
        }

        return $json;
    }

    /**
     * @param array|object $json
     */
    final public static function jsonUnserialize($json): self
    {
        if (! is_array($json) && ! is_object($json)) {
            throw UnserializationException::invalidValue('GeoJson', $json, 'array or object');
        }

        $json = new ArrayObject($json);

        if (! $json->offsetExists('type')) {
            throw UnserializationException::missingProperty('GeoJson', 'type', 'string');
        }

        $type = (string) $json['type'];
        $args = [];

        switch ($type) {
            case 'LineString':
            case 'MultiLineString':
            case 'MultiPoint':
            case 'MultiPolygon':
            case 'Point':
            case 'Polygon':
                if (! $json->offsetExists('coordinates')) {
                    throw UnserializationException::missingProperty($type, 'coordinates', 'array');
                }

                if (! is_array($json['coordinates'])) {
                    throw UnserializationException::invalidProperty($type, 'coordinates', $json['coordinates'], 'array');
                }

                $args[] = $json['coordinates'];
                break;

            case 'Feature':
                $geometry = $json['geometry'] ?? null;
                $properties = $json['properties'] ?? null;
                $id = $json['id'] ?? null;

                if ($geometry !== null && ! is_array($geometry) && ! is_object($geometry)) {
                    throw UnserializationException::invalidProperty($type, 'geometry', $geometry, 'array or object');
                }

                if ($properties !== null && ! is_array($properties) && ! is_object($properties)) {
                    throw UnserializationException::invalidProperty($type, 'properties', $properties, 'array or object');
                }

                // TODO: Validate non-null $id as int or string in 2.0

                $args[] = $geometry !== null ? self::jsonUnserialize($geometry) : null;
                $args[] = $properties !== null ? (array) $properties : null;
                $args[] = $id;
                break;

            case 'FeatureCollection':
                if (! $json->offsetExists('features')) {
                    throw UnserializationException::missingProperty($type, 'features', 'array');
                }

                if (! is_array($json['features'])) {
                    throw UnserializationException::invalidProperty($type, 'features', $json['features'], 'array');
                }

                $args[] = array_map(['self', 'jsonUnserialize'], $json['features']);
                break;

            case 'GeometryCollection':
                if (! $json->offsetExists('geometries')) {
                    throw UnserializationException::missingProperty($type, 'geometries', 'array');
                }

                if (! is_array($json['geometries'])) {
                    throw UnserializationException::invalidProperty($type, 'geometries', $json['geometries'], 'array');
                }

                $args[] = array_map(['self', 'jsonUnserialize'], $json['geometries']);
                break;

            default:
                throw UnserializationException::unsupportedType('GeoJson', $type);
        }

        if (isset($json['bbox'])) {
            $args[] = BoundingBox::jsonUnserialize($json['bbox']);
        }

        if (isset($json['crs'])) {
            $args[] = CoordinateReferenceSystem::jsonUnserialize($json['crs']);
        }

        $class = sprintf('GeoJson\%s\%s', (strncmp('Feature', $type, 7) === 0 ? 'Feature' : 'Geometry'), $type);

        return new $class(... $args);
    }

    /**
     * Set optional CRS and BoundingBox arguments passed to a constructor.
     *
     * @todo Decide if multiple CRS or BoundingBox instances should override a
     *       previous value or be ignored
     */
    protected function setOptionalConstructorArgs(array $args): void
    {
        foreach ($args as $arg) {
            if ($arg instanceof CoordinateReferenceSystem) {
                $this->crs = $arg;
            }

            if ($arg instanceof BoundingBox) {
                $this->boundingBox = $arg;
            }
        }
    }
}
