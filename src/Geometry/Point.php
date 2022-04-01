<?php

declare(strict_types=1);

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use InvalidArgumentException;

use function count;
use function is_float;
use function is_int;

/**
 * Point geometry object.
 *
 * Coordinates consist of a single position.
 *
 * @see http://www.geojson.org/geojson-spec.html#point
 * @since 1.0
 */
class Point extends Geometry
{
    protected string $type = 'Point';

    /**
     * @param array<float|int> $position
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(array $position, ...$args)
    {
        if (count($position) < 2) {
            throw new InvalidArgumentException('Position requires at least two elements');
        }

        foreach ($position as $value) {
            if (! is_int($value) && ! is_float($value)) {
                throw new InvalidArgumentException('Position elements must be integers or floats');
            }
        }

        $this->coordinates = $position;

        $this->setOptionalConstructorArgs($args);
    }
}
