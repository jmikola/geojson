<?php

declare(strict_types=1);

namespace GeoJson\Geometry;

use ArrayIterator;
use Countable;
use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

use function array_map;
use function array_merge;
use function array_values;
use function count;

/**
 * Collection of Geometry objects.
 *
 * @see http://www.geojson.org/geojson-spec.html#geometry-collection
 * @since 1.0
 */
class GeometryCollection extends Geometry implements Countable, IteratorAggregate
{
    protected string $type = 'GeometryCollection';

    /**
     * @var array<Geometry>
     */
    protected array $geometries;

    /**
     * @param array<Geometry> $geometries
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(array $geometries, ...$args)
    {
        foreach ($geometries as $geometry) {
            if (! $geometry instanceof Geometry) {
                throw new InvalidArgumentException('GeometryCollection may only contain Geometry objects');
            }
        }

        $this->geometries = array_values($geometries);

        $this->setOptionalConstructorArgs($args);
    }

    public function count(): int
    {
        return count($this->geometries);
    }

    /**
     * Return the Geometry objects in this collection.
     *
     * @return array<Geometry>
     */
    public function getGeometries(): array
    {
        return $this->geometries;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->geometries);
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            ['geometries' => array_map(
                static fn(Geometry $geometry) => $geometry->jsonSerialize(),
                $this->geometries
            )]
        );
    }
}
