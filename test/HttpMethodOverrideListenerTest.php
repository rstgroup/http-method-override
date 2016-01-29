<?php

namespace RstGroup\HttpMethodOverride\Test;

use PHPUnit_Framework_TestCase;
use RstGroup\HttpMethodOverride\HttpMethodOverrideListener;
use RstGroup\HttpMethodOverride\HttpMethodOverrideService;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface;

class HttpMethodOverrideListenerTest extends PHPUnit_Framework_TestCase
{
    protected $listener;

    protected function setUp()
    {
        $service = new HttpMethodOverrideService(['POST' => ['PUT']]);

        $this->listener = new HttpMethodOverrideListener($service);
    }

    public function testOverride()
    {
        $request = new Request();
        $request->setMethod('POST');

        $request->getHeaders()->addHeaderLine(HttpMethodOverrideService::OVERRIDE_HEADER, 'PUT');

        $event = new MvcEvent();
        $event->setRequest($request);

        $this->listener->override($event);

        $this->assertSame('PUT', $request->getMethod());
    }

    public function testNotOverride()
    {
        $request = new Request();
        $request->setMethod('POST');

        $request->getHeaders()->addHeaderLine(HttpMethodOverrideService::OVERRIDE_HEADER, 'GET');

        $event = new MvcEvent();
        $event->setRequest($request);

        $this->listener->override($event);

        $this->assertSame('POST', $request->getMethod());
    }

    public function testNoneHtmlRequest()
    {
        $request = $this->getMock(RequestInterface::class);

        $event = new MvcEvent();
        $event->setRequest($request);

        $clonedRequest = clone $request;

        $this->listener->override($event);

        $this->assertEquals($clonedRequest, $request);
    }

    public function testAttach()
    {
        $eventManager = $this->getMock(EventManagerInterface::class);
        $eventManager->expects($this->once())->method('attach')->willReturnCallback(function($name, $callback){
            $this->assertTrue(is_callable($callback));
        });

        $this->listener->attach($eventManager);
    }
}