<?php

declare(strict_types=1);

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use InvalidArgumentException;

use function count;
use function end;
use function reset;

/**
 * LinearRing is a special kind of LineString geometry object.
 *
 * Coordinates consist of an array of at least four positions, where the first
 * and last positions are equivalent.
 *
 * @see http://www.geojson.org/geojson-spec.html#linestring
 * @since 1.0
 */
class LinearRing extends LineString
{
    /**
     * @param array<Point|array<int|float>> $positions
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(array $positions, ...$args)
    {
        if (count($positions) < 4) {
            throw new InvalidArgumentException('LinearRing requires at least four positions');
        }

        $lastPosition = end($positions);
        $firstPosition = reset($positions);

        $lastPosition = $lastPosition instanceof Point ? $lastPosition->getCoordinates() : $lastPosition;
        $firstPosition = $firstPosition instanceof Point ? $firstPosition->getCoordinates() : $firstPosition;

        if ($lastPosition !== $firstPosition) {
            throw new InvalidArgumentException('LinearRing requires the first and last positions to be equivalent');
        }

        parent::__construct($positions, ... $args);
    }
}
