<?php

namespace GeoJson\Tests;

use PHPUnit\Framework\TestCase;
use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\Feature\Feature;
use GeoJson\Geometry\Geometry;

abstract class BaseGeoJsonTest extends TestCase
{
    abstract public function createSubjectWithExtraArguments(array $extraArgs);

    public function testConstructorShouldScanExtraArgumentsForCrsAndBoundingBox()
    {
        $box = $this->getMockBoundingBox();
        $crs = $this->getMockCoordinateReferenceSystem();

        $sut = $this->createSubjectWithExtraArguments(array());
        $this->assertNull($sut->getBoundingBox());
        $this->assertNull($sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments(array($box));
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertNull($sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments(array($crs));
        $this->assertNull($sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments(array($box, $crs));
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments(array($crs, $box));
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());

        // Not that you would, but you could…
        $sut = $this->createSubjectWithExtraArguments(array(null, null, $box, $crs));
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());
    }

    public function testSerializationWithCrsAndBoundingBox()
    {
        $box = $this->getMockBoundingBox();
        $box->method('jsonSerialize')->willReturn(['boundingBox']);

        $crs = $this->getMockCoordinateReferenceSystem();
        $crs->method('jsonSerialize')->willReturn(['coordinateReferenceSystem']);

        $sut = $this->createSubjectWithExtraArguments(array($box, $crs));

        $json = $sut->jsonSerialize();

        $this->assertArrayHasKey('bbox', $json);
        $this->assertArrayHasKey('crs', $json);
        $this->assertSame(['boundingBox'], $json['bbox']);
        $this->assertSame(['coordinateReferenceSystem'], $json['crs']);
    }

    protected function getMockBoundingBox()
    {
        return $this->getMockBuilder(BoundingBox::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockCoordinateReferenceSystem()
    {
        return $this->getMockBuilder(CoordinateReferenceSystem::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockFeature()
    {
        return $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockGeometry()
    {
        return $this->getMockBuilder(Geometry::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
