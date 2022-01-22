<?php

namespace GeoJson\Geometry;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

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
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $geometries)
    {
        foreach ($geometries as $geometry) {
            if ( ! $geometry instanceof Geometry) {
                throw new InvalidArgumentException('GeometryCollection may only contain Geometry objects');
            }
        }

        $this->geometries = array_values($geometries);

        if (func_num_args() > 1) {
            $this->setOptionalConstructorArgs(array_slice(func_get_args(), 1));
        }
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
            array('geometries' => array_map(
                function(Geometry $geometry) { return $geometry->jsonSerialize(); },
                $this->geometries
            ))
        );
    }
}
