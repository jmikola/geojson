# GeoJson PHP Library

This library implements the
[GeoJSON format specification](https://geojson.org/).

The `GeoJson` namespace includes classes for each data structure defined in the
GeoJSON specification. Core GeoJSON objects include geometries, features, and
collections. Geometries range from primitive points to more complex polygons.
Classes also exist for bounding boxes and coordinate reference systems.

## Installation

The library is published as a
[package](https://packagist.org/packages/jmikola/geojson) and is installable via
[Composer](https://getcomposer.org/):

```
$ composer require "jmikola/geojson=^1.0"
```

## Usage

Classes in this library are immutable.

### GeoJson Constructors

Geometry objects are constructed using a single coordinates array. This may be
a tuple in the case of a `Point`, an array of tuples for a `LineString`, etc.
Constructors for each class will validate the coordinates array and throw an
`InvalidArgumentException` on error.

More primitive geometry objects may also be used for constructing complex
objects. For instance, a `LineString` may be constructed from an array of
`Point` objects.

Feature objects are constructed from a geometry object, associative properties
array, and an identifier, all of which are optional.

Feature and geometry collection objects are constructed from an array of their
respective types.

#### Specifying a Bounding Box or CRS

All GeoJson constructors support `BoundingBox` and `CoordinateReferenceSystem`
objects as optional arguments beyond those explicitly listed in their prototype.
These objects may appear in any order *after* the explicit arguments.

```php
$crs = new \GeoJson\CoordinateReferenceSystem\Named('urn:ogc:def:crs:OGC:1.3:CRS84');
$box = new \GeoJson\BoundingBox([-180, -90, 180, 90]);
$point = new \GeoJson\Geometry\Point([0, 0], $crs, $box);
```

Note that the `Feature` class is unique in that it has three arguments, all with
default values. In order to construct a `Feature` with a bounding box or CRS,
all three arguments must be explicitly listed (e.g. with `null` placeholders).

```php
$box = new \GeoJson\BoundingBox([-180, -90, 180, 90]);
$feature = new \GeoJson\Feature\Feature(null, null, null, $box);
```

### JSON Serialization

Each class in the library implements the
[JsonSerializable](https://php.net/manual/en/class.jsonserializable.php)
interface, which allows objects to be passed directly to `json_encode()`.

```php
$point = new \GeoJson\Geometry\Point([1, 1]);
$json = json_encode($point);
```

Printing the `$json` variable would yield (sans whitespace):

```json
{
    "type": "Point",
    "coordinates": [1, 1]
}
```

### JSON Unserialization

The core `GeoJson` class implements an internal `JsonUnserializable` interface,
which defines a static factory method, `jsonUnserialize()`, that can be used to
create objects from the return value of `json_decode()`.

```php
$json = '{ "type": "Point", "coordinates": [1, 1] }';
$json = json_decode($json);
$point = \GeoJson\GeoJson::jsonUnserialize($json);
```

If errors are encountered during unserialization, an `UnserializationException`
will be thrown by `jsonUnserialize()`. Possible errors include:

 * Missing properties (e.g. `type` is not present)
 * Unexpected values (e.g. `coordinates` property is not an array)
 * Unsupported `type` string when parsing a GeoJson object or CRS
