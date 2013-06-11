<?php

namespace GeoJson;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;

/**
 * Base GeoJson object.
 *
 * @see http://www.geojson.org/geojson-spec.html#geojson-objects
 * @since 1.0
 */
abstract class GeoJson implements \JsonSerializable
{
    /**
     * @var BoundingBox
     */
    protected $boundingBox;

    /**
     * @var CoordinateReferenceSystem
     */
    protected $crs;

    /**
     * @var string
     */
    protected $type;

    /**
     * Return the BoundingBox for this GeoJson object.
     *
     * @return BoundingBox
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }

    /**
     * Return the CoordinateReferenceSystem for this GeoJson object.
     *
     * @return CoordinateReferenceSystem
     */
    public function getCrs()
    {
        return $this->crs;
    }

    /**
     * Return the type for this GeoJson object.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        $json = array('type' => $this->type);

        if (isset($this->crs)) {
            $json['crs'] = $this->crs->jsonSerialize();
        }

        if (isset($this->boundingBox)) {
            $json['bbox'] = $this->boundingBox->jsonSerialize();
        }

        return $json;
    }

    /**
     * Set optional CRS and BoundingBox arguments passed to a constructor.
     *
     * @todo Decide if multiple CRS or BoundingBox instances should override a
     *       previous value or be ignored
     */
    protected function setOptionalConstructorArgs(array $args)
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
