<?php

namespace RstGroup\HttpMethodOverride\Test;

use PHPUnit\Framework\TestCase;
use RstGroup\HttpMethodOverride\HttpMethodOverrideListener;
use RstGroup\HttpMethodOverride\HttpMethodOverrideService;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface;

class HttpMethodOverrideListenerTest extends TestCase
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

    public function testEventContainsOverridedMethod()
    {
        $request = new Request();
        $request->setMethod('POST');

        $request->getHeaders()->addHeaderLine(HttpMethodOverrideService::OVERRIDE_HEADER, 'PUT');

        $event = new MvcEvent();
        $event->setRequest($request);

        $this->listener->override($event);

        $this->assertSame('POST', $event->getParam('overrided-method'));
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
        $request = $this->createMock(RequestInterface::class);

        $event = new MvcEvent();
        $event->setRequest($request);

        $clonedRequest = clone $request;

        $this->listener->override($event);

        $this->assertEquals($clonedRequest, $request);
    }

    public function testAttach()
    {
        $eventManager = $this->createMock(EventManagerInterface::class);
        $eventManager->expects($this->once())->method('attach')->willReturnCallback(function($name, $callback){
            $this->assertTrue(is_callable($callback));
        });

        $this->listener->attach($eventManager);
    }
}