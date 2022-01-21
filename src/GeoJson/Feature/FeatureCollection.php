<?php

namespace GeoJson\Feature;

use ArrayIterator;
use Countable;
use GeoJson\GeoJson;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

/**
 * Collection of Feature objects.
 *
 * @see http://www.geojson.org/geojson-spec.html#feature-collection-objects
 * @since 1.0
 */
class FeatureCollection extends GeoJson implements Countable, IteratorAggregate
{
    protected $type = 'FeatureCollection';

    /**
     * @var array
     */
    protected $features;

    /**
     * Constructor.
     *
     * @param Feature[] $features
     * @param CoordinateResolutionSystem|BoundingBox $arg,...
     */
    public function __construct(array $features)
    {
        foreach ($features as $feature) {
            if ( ! $feature instanceof Feature) {
                throw new InvalidArgumentException('FeatureCollection may only contain Feature objects');
            }
        }

        $this->features = array_values($features);

        if (func_num_args() > 1) {
            $this->setOptionalConstructorArgs(array_slice(func_get_args(), 1));
        }
    }

    public function count(): int
    {
        return count($this->features);
    }

    /**
     * Return the Feature objects in this collection.
     *
     * @return Feature[]
     */
    public function getFeatures()
    {
        return $this->features;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->features);
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            array('features' => array_map(
                function(Feature $feature) { return $feature->jsonSerialize(); },
                $this->features
            ))
        );
    }
}
