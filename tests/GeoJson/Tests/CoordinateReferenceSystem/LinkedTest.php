<?php

declare(strict_types=1);

namespace GeoJson\Tests\CoordinateReferenceSystem;

use GeoJson\CoordinateReferenceSystem\CoordinateReferenceSystem;
use GeoJson\CoordinateReferenceSystem\Linked;
use GeoJson\Exception\UnserializationException;
use PHPUnit\Framework\TestCase;

use function is_subclass_of;
use function json_decode;

class LinkedTest extends TestCase
{
    public function testIsSubclassOfCoordinateReferenceSystem(): void
    {
        $this->assertTrue(is_subclass_of(Linked::class, CoordinateReferenceSystem::class));
    }

    public function testSerialization(): void
    {
        $crs = new Linked('https://example.com/crs/42', 'proj4');

        $expected = [
            'type' => 'link',
            'properties' => [
                'href' => 'https://example.com/crs/42',
                'type' => 'proj4',
            ],
        ];

        $this->assertSame('link', $crs->getType());
        $this->assertSame($expected['properties'], $crs->getProperties());
        $this->assertSame($expected, $crs->jsonSerialize());
    }

    public function testSerializationWithoutHrefType(): void
    {
        $crs = new Linked('https://example.com/crs/42');

        $expected = [
            'type' => 'link',
            'properties' => [
                'href' => 'https://example.com/crs/42',
            ],
        ];

        $this->assertSame($expected, $crs->jsonSerialize());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserialization($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "link",
    "properties": {
        "href": "https://example.com/crs/42",
        "type": "proj4"
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $crs = CoordinateReferenceSystem::jsonUnserialize($json);

        $expectedProperties = [
            'href' => 'https://example.com/crs/42',
            'type' => 'proj4',
        ];

        $this->assertInstanceOf(Linked::class, $crs);
        $this->assertSame('link', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    /**
     * @dataProvider provideJsonDecodeAssocOptions
     * @group functional
     */
    public function testUnserializationWithoutHrefType($assoc): void
    {
        $json = <<<'JSON'
{
    "type": "link",
    "properties": {
        "href": "https://example.com/crs/42"
    }
}
JSON;

        $json = json_decode($json, $assoc);
        $crs = CoordinateReferenceSystem::jsonUnserialize($json);

        $expectedProperties = ['href' => 'https://example.com/crs/42'];

        $this->assertInstanceOf(Linked::class, $crs);
        $this->assertSame('link', $crs->getType());
        $this->assertSame($expectedProperties, $crs->getProperties());
    }

    public function provideJsonDecodeAssocOptions()
    {
        return [
            'assoc=true' => [true],
            'assoc=false' => [false],
        ];
    }

    public function testUnserializationShouldRequirePropertiesArrayOrObject(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Linked CRS expected "properties" property of type array or object');

        CoordinateReferenceSystem::jsonUnserialize(['type' => 'link', 'properties' => null]);
    }

    public function testUnserializationShouldRequireHrefProperty(): void
    {
        $this->expectException(UnserializationException::class);
        $this->expectExceptionMessage('Linked CRS expected "properties.href" property of type string');

        CoordinateReferenceSystem::jsonUnserialize(['type' => 'link', 'properties' => []]);
    }
}
