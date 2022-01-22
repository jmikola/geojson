<?php

declare(strict_types=1);

namespace GeoJson\Geometry;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

use function array_map;

/**
 * MultiLineString geometry object.
 *
 * Coordinates consist of an array of LineString coordinates.
 *
 * @see http://www.geojson.org/geojson-spec.html#multilinestring
 * @since 1.0
 */
class MultiLineString extends Geometry
{
    protected string $type = 'MultiLineString';

    /**
     * @param array<LineString|array<Point|array<int|float>>> $lineStrings
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(array $lineStrings, ...$args)
    {
        $this->coordinates = array_map(
            static function ($lineString) {
                if (! $lineString instanceof LineString) {
                    $lineString = new LineString($lineString);
                }

                return $lineString->getCoordinates();
            },
            $lineStrings
        );

        $this->setOptionalConstructorArgs($args);
    }
}
