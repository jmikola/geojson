<?php

declare(strict_types=1);

namespace GeoJson;

use GeoJson\Exception\UnserializationException;
use InvalidArgumentException;
use JsonSerializable;

use function count;
use function is_array;
use function is_float;
use function is_int;

/**
 * BoundingBox object.
 *
 * @see http://www.geojson.org/geojson-spec.html#bounding-boxes
 * @since 1.0
 */
class BoundingBox implements JsonSerializable, JsonUnserializable
{
    /**
     * @var array<float|int>
     */
    protected array $bounds;

    /**
     * @param array<float|int> $bounds
     */
    public function __construct(array $bounds)
    {
        $count = count($bounds);

        if ($count < 4) {
            throw new InvalidArgumentException('BoundingBox requires at least four values');
        }

        if ($count % 2) {
            throw new InvalidArgumentException('BoundingBox requires an even number of values');
        }

        foreach ($bounds as $value) {
            if (! is_int($value) && ! is_float($value)) {
                throw new InvalidArgumentException('BoundingBox values must be integers or floats');
            }
        }

        for ($i = 0; $i < ($count / 2); $i++) {
            if ($bounds[$i] > $bounds[$i + ($count / 2)]) {
                throw new InvalidArgumentException('BoundingBox min values must precede max values');
            }
        }

        $this->bounds = $bounds;
    }

    /**
     * Return the bounds for this BoundingBox object.
     *
     * @return array<float|int>
     */
    public function getBounds(): array
    {
        return $this->bounds;
    }

    public function jsonSerialize(): array
    {
        return $this->bounds;
    }

    /**
     * @param array $json
     */
    final public static function jsonUnserialize($json): self
    {
        if (! is_array($json)) {
            throw UnserializationException::invalidValue('BoundingBox', $json, 'array');
        }

        return new self($json);
    }
}
