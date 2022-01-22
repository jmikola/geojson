<?php

declare(strict_types=1);

namespace GeoJson\Tests;

use GeoJson\BoundingBox;
use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\Feature\Feature;
use GeoJson\Geometry\Geometry;
use PHPUnit\Framework\TestCase;

abstract class BaseGeoJsonTest extends TestCase
{
    /**
     * @param ...$extraArgs
     *
     * @return mixed
     */
    abstract public function createSubjectWithExtraArguments(...$extraArgs);

    public function testConstructorShouldScanExtraArgumentsForCrsAndBoundingBox(): void
    {
        $box = $this->getMockBoundingBox();
        $crs = $this->getMockCoordinateReferenceSystem();

        $sut = $this->createSubjectWithExtraArguments();
        $this->assertNull($sut->getBoundingBox());
        $this->assertNull($sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments($box);
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertNull($sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments($crs);
        $this->assertNull($sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments($box, $crs);
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());

        $sut = $this->createSubjectWithExtraArguments($crs, $box);
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());

        // Not that you would, but you could…
        $sut = $this->createSubjectWithExtraArguments(null, null, $box, $crs);
        $this->assertSame($box, $sut->getBoundingBox());
        $this->assertSame($crs, $sut->getCrs());
    }

    public function testSerializationWithCrsAndBoundingBox(): void
    {
        $box = $this->getMockBoundingBox();
        $box->method('jsonSerialize')->willReturn(['boundingBox']);

        $crs = $this->getMockCoordinateReferenceSystem();
        $crs->method('jsonSerialize')->willReturn(['coordinateReferenceSystem']);

        $sut = $this->createSubjectWithExtraArguments($box, $crs);

        $json = $sut->jsonSerialize();

        $this->assertArrayHasKey('bbox', $json);
        $this->assertArrayHasKey('crs', $json);
        $this->assertSame(['boundingBox'], $json['bbox']);
        $this->assertSame(['coordinateReferenceSystem'], $json['crs']);
    }

    protected function getMockBoundingBox()
    {
        return $this->createMock(BoundingBox::class);
    }

    protected function getMockCoordinateReferenceSystem()
    {
        return $this->createMock(CoordinateReferenceSystem::class);
    }

    protected function getMockFeature()
    {
        return $this->createMock(Feature::class);
    }

    protected function getMockGeometry()
    {
        return $this->createMock(Geometry::class);
    }
}
