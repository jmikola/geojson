<?php

declare(strict_types=1);

namespace GeoJson\Feature;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\GeoJson;
use GeoJson\Geometry\Geometry;
use stdClass;

/**
 * Feature object.
 *
 * @see http://www.geojson.org/geojson-spec.html#feature-objects
 * @since 1.0
 */
class Feature extends GeoJson
{
    protected string $type = 'Feature';

    protected ?Geometry $geometry;

    /**
     * Properties are a JSON object, which corresponds to an associative array, or null.
     *
     * @see https://www.rfc-editor.org/rfc/rfc7946#section-3.2
     */
    protected ?array $properties;

    /**
     * The identifier is either a JSON string or a number.
     *
     * @see https://www.rfc-editor.org/rfc/rfc7946#section-3.2
     *
     * @var int|string|null
     */
    protected $id;

    /**
     * @param int|string|null $id
     * @param CoordinateReferenceSystem|BoundingBox $args
     */
    public function __construct(?Geometry $geometry = null, ?array $properties = null, $id = null, ...$args)
    {
        $this->geometry = $geometry;
        $this->properties = $properties;
        $this->id = $id;

        $this->setOptionalConstructorArgs($args);
    }

    /**
     * Return the Geometry object for this Feature object.
     */
    public function getGeometry(): ?Geometry
    {
        return $this->geometry;
    }

    /**
     * Return the identifier for this Feature object.
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the properties for this Feature object.
     */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();

        $json['geometry'] = isset($this->geometry) ? $this->geometry->jsonSerialize() : null;
        $json['properties'] = $this->properties ?? null;

        // Ensure empty associative arrays are encoded as JSON objects
        if ($json['properties'] === []) {
            $json['properties'] = new stdClass();
        }

        if (isset($this->id)) {
            $json['id'] = $this->id;
        }

        return $json;
    }
}
