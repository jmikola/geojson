# GeoJson PHP Library

[![Build Status](https://github.com/jmikola/geojson/workflows/Continuous%20Integration/badge.svg)](https://github.com/jmikola/geojson/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jmikola/geojson/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jmikola/geojson/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jmikola/geojson/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jmikola/geojson/?branch=master)

This library implements the
[GeoJSON format specification](http://www.geojson.org/geojson-spec.html).

The `GeoJson` namespace includes classes for each data structure defined in the
GeoJSON specification. Core GeoJSON objects include geometries, features, and
collections. Geometries range from primitive points to more complex polygons.
Classes also exist for bounding boxes and coordinate reference systems.

## Installation

The library is published as a
[package](https://packagist.org/packages/jmikola/geojson) and is installable via
[Composer](http://getcomposer.org/):

```
$ composer require "jmikola/geojson=~1.0"
```

## More Resources

 * [Documentation](http://jmikola.github.io/geojson)
