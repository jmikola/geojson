<?php

namespace GeoJson\Geometry;

/**
 * Collection of Geometry objects.
 *
 * @see http://www.geojson.org/geojson-spec.html#geometry-collection
 * @since 1.0
 */
class GeometryCollection extends Geometry implements \Countable, \IteratorAggregate
{
    protected $type = 'GeometryCollection';

    /**
     * @var array
     */
    protected $geometries;

    /**
     * Constructor.
     *
     * @param Geometry[] $geometries
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $geometries)
    {
        foreach ($geometries as $geometry) {
            if ( ! $geometry instanceof Geometry) {
                throw new \InvalidArgumentException('GeometryCollection may only contain Geometry objects');
            }
        }

        $this->geometries = array_values($geometries);

        if (func_num_args() > 1) {
            $this->setOptionalConstructorArgs(array_slice(func_get_args(), 1));
        }
    }

    /**
     * @see http://php.net/manual/en/countable.count.php
     */
    public function count()
    {
        return count($this->geometries);
    }

    /**
     * Return the Geometry objects in this collection.
     *
     * @return Geometry[]
     */
    public function getGeometries()
    {
        return $this->geometries;
    }

    /**
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->geometries);
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
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
