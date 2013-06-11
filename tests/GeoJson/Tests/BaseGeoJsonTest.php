<?php

namespace GeoJson\Tests;

abstract class BaseGeoJsonTest extends \PHPUnit_Framework_TestCase
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
        $crs = $this->getMockCoordinateReferenceSystem();

        $box->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue('boundingBox'));

        $crs->expects($this->any())
            ->method('jsonSerialize')
            ->will($this->returnValue('coordinateReferenceSystem'));

        $sut = $this->createSubjectWithExtraArguments(array($box, $crs));

        $json = $sut->jsonSerialize();

        $this->assertArrayHasKey('bbox', $json);
        $this->assertArrayHasKey('crs', $json);
        $this->assertSame('boundingBox', $json['bbox']);
        $this->assertSame('coordinateReferenceSystem', $json['crs']);
    }

    protected function getMockBoundingBox()
    {
        return $this->getMockBuilder('GeoJson\BoundingBox')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockCoordinateReferenceSystem()
    {
        return $this->getMockBuilder('GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockFeature()
    {
        return $this->getMockBuilder('GeoJson\Feature\Feature')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockGeometry()
    {
        return $this->getMockBuilder('GeoJson\Geometry\Geometry')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
