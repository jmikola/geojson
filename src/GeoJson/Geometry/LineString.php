<?php

declare(strict_types=1);

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use InvalidArgumentException;

use function count;

/**
 * LineString geometry object.
 *
 * Coordinates consist of an array of at least two positions.
 *
 * @see http://www.geojson.org/geojson-spec.html#linestring
 * @since 1.0
 */
class LineString extends MultiPoint
{
    protected string $type = 'LineString';

    /**
     * @param array<Point|array<float|int>> $positions
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(array $positions, ...$args)
    {
        if (count($positions) < 2) {
            throw new InvalidArgumentException('LineString requires at least two positions');
        }

        parent::__construct($positions, ... $args);
    }
}
