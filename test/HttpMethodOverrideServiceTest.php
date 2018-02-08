<?php

namespace RstGroup\HttpMethodOverride\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RstGroup\HttpMethodOverride\HttpMethodOverrideService;

class HttpMethodOverrideServiceTest extends TestCase
{
    /**
     * @dataProvider notmethodInMapProvider
     */
    public function testNotMethodInMap(array $methodMap)
    {
        $object = new HttpMethodOverrideService($methodMap);

        $result = $object->getOverridedMethod('GET', []);

        $this->assertSame('GET', $result);
    }

    public function notMethodInMapProvider()
    {
        return [
            [[]],
            [['POST' => 'NONE']],
        ];
    }

    /**
     * @dataProvider overrideProvider
     */
    public function testOverride($methodRequest, $methodOverride, $methodMap)
    {
        $object = new HttpMethodOverrideService($methodMap);

        $result = $object->getOverridedMethod($methodRequest, [
            HttpMethodOverrideService::OVERRIDE_HEADER_GOOGLE => $methodOverride,
        ]);

        $this->assertSame($methodOverride, $result);
    }

    public function overrideProvider()
    {
        return [
            ['POST', 'LINK', ['POST' => ['LINK']]],
            ['POST', 'UNLINK', ['GET' => ['OPTIONS'], 'POST' => ['LINK', 'UNLINK']]],
        ];
    }

    public function testOverrideWitoutHeader()
    {
        $object = new HttpMethodOverrideService(['POST' => ['NONE']]);

        $result = $object->getOverridedMethod('POST', [
            HttpMethodOverrideService::OVERRIDE_HEADER_GOOGLE => 'OTHER',
        ]);

        $this->assertNotEquals('NONE', $result);
    }

    public function testOverrideHeaderIsNotCaseSensitive()
    {
        $object = new HttpMethodOverrideService(['POST' => ['PUT']]);

        $result = $object->getOverridedMethod('POST', [
            strtolower(HttpMethodOverrideService::OVERRIDE_HEADER_GOOGLE) => 'PUT',
        ]);

        $this->assertSame('PUT', $result);
    }

    public function testOverrideWithHeaderButNotInMap()
    {
        $object = new HttpMethodOverrideService(['POST' => ['NONE']]);

        $result = $object->getOverridedMethod('POST', []);

        $this->assertSame('POST', $result);
    }

    public function testMapIsNotAnArray()
    {
        $object = new HttpMethodOverrideService(['POST' => 'NONE']);

        $this->expectException(InvalidArgumentException::class);

        $object->getOverridedMethod('POST', [
            HttpMethodOverrideService::OVERRIDE_HEADER_GOOGLE => 'NONE',
        ]);
    }

    public function testCustomHeaderName()
    {
        $object = new HttpMethodOverrideService(['POST' => ['NONE']], ['Custom-header']);

        $result = $object->getOverridedMethod('POST', [
            'Custom-header' => 'NONE',
        ]);

        $this->assertSame('NONE', $result);
    }
}