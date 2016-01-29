<?php

namespace RstGroup\HttpMethodOverride\Test;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use RstGroup\HttpMethodOverride\HttpMethodOverride;


class HttpMethodOverrideTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderInvalid
     */
    public function testAllowtoAddEmptyArray($methodMap)
    {
        $object = new HttpMethodOverride($methodMap);

        $request = $this->getRequestMock();
        $headers = $this->getHeadersMock();
        $headers->expects($this->any())->method('has')->willReturn(false);
        $request->expects($this->once())->method('getHeaders')->willReturn($headers);
        $request->expects($this->never())->method('setMethod');

        $result = $object->override($request);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderValid
     */
    public function testOverride($methodRequest, $methodOverride, $methodMap)
    {
        $object = new HttpMethodOverride($methodMap);

        $header = $this->getHeaderMock();
        $header->expects($this->once())->method('getFieldValue')->willReturn($methodOverride);

        $headers = $this->getHeadersMock();
        $headers->expects($this->once())->method('has')->with(HttpMethodOverride::OVERRIDE_HEADER_GOOGLE)->willReturn(true);
        $headers->expects($this->once())->method('get')->with(HttpMethodOverride::OVERRIDE_HEADER_GOOGLE)->willReturn($header);

        $request = $this->getRequestMock();
        $request->expects($this->once())->method('setMethod')->with($methodOverride);
        $request->expects($this->once())->method('getMethod')->with()->willReturn($methodRequest);
        $request->expects($this->once())->method('getHeaders')->willReturn($headers);

        $result = $object->override($request);

        $this->assertTrue($result);
    }

    public function testOverrideWitoutHeader()
    {
        $object = new HttpMethodOverride(array('POST' => array('NONE')));

        $header = $this->getHeaderMock();
        $header->expects($this->once())->method('getFieldValue')->willReturn('OTHER');

        $headers = $this->getHeadersMock();
        $headers->expects($this->once())->method('has')->with(HttpMethodOverride::OVERRIDE_HEADER_GOOGLE)->willReturn(true);
        $headers->expects($this->once())->method('get')->with(HttpMethodOverride::OVERRIDE_HEADER_GOOGLE)->willReturn($header);

        $request = $this->getRequestMock();
        $request->expects($this->once())->method('getMethod')->with()->willReturn('POST');
        $request->expects($this->once())->method('getHeaders')->willReturn($headers);

        $result = $object->override($request);

        $this->assertFalse($result);
    }

    public function testOverrideWithHeaderButNotInMap()
    {
        $object = new HttpMethodOverride(array('POST' => 'NONE'));

        $headers = $this->getHeadersMock();
        $headers->expects($this->any())->method('has')->willReturn(false);

        $request = $this->getRequestMock();
        $request->expects($this->once())->method('getMethod')->with()->willReturn('POST');
        $request->expects($this->once())->method('getHeaders')->willReturn($headers);

        $result = $object->override($request);

        $this->assertFalse($result);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Method map must be an array
     */
    public function testMapIsNotAnArray()
    {
        $object = new HttpMethodOverride(array('POST' => 'NONE'));

        $header = $this->getHeaderMock();
        $header->expects($this->once())->method('getFieldValue')->willReturn('NONE');

        $headers = $this->getHeadersMock();
        $headers->expects($this->any())->method('has')->willReturn(true);
        $headers->expects($this->any())->method('get')->willReturn($header);

        $request = $this->getRequestMock();
        $request->expects($this->once())->method('getMethod')->with()->willReturn('POST');
        $request->expects($this->once())->method('getHeaders')->willReturn($headers);

        $object->override($request);
    }

    public function dataProviderValid()
    {
        return array(
            array('POST', 'LINK', array('POST' => array('LINK'))),
            array('POST', 'UNLINK', array('GET' => array('OPTIONS'), 'POST' => array('LINK', 'UNLINK'))),
        );
    }

    public function dataProviderInvalid()
    {
        return array(
            array(array()),
            array(array('POST' => 'NONE')),
        );
    }

    protected function getRequestMock()
    {
        return $this->getMockBuilder('Zend\Http\Request')->getMock();
    }

    protected function getHeaderMock()
    {
        return $this->getMockBuilder('Zend\Http\Header\HeaderInterface')->getMock();
    }

    protected function getHeadersMock()
    {
        return $this->getMockBuilder('Zend\Http\Headers')->getMock();
    }
}
