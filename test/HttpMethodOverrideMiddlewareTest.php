<?php

namespace RstGroup\HttpMethodOverride\Test;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RstGroup\HttpMethodOverride\HttpMethodOverrideMiddleware;
use RstGroup\HttpMethodOverride\HttpMethodOverrideService;
use Zend\Diactoros\ServerRequest;

class HttpMethodOverrideMiddlewareTest extends PHPUnit_Framework_TestCase
{
    protected $middleware;

    protected function setUp()
    {
        $service = new HttpMethodOverrideService(['POST' => ['PUT']]);

        $this->middleware = new HttpMethodOverrideMiddleware($service);
    }

    public function testCallNext()
    {
        $request = $this->getMock(ServerRequestInterface::class);
        $response = $this->getMock(ResponseInterface::class);

        $next = function(ServerRequestInterface $request, ResponseInterface $response)  {
            return $response;
        };

        $result = call_user_func($this->middleware, $request, $response, $next);
        
        $this->assertSame($response, $result);
    }

    public function testOverride()
    {
        $request = new ServerRequest();
        $request = $request->withHeader(HttpMethodOverrideService::OVERRIDE_HEADER, 'PUT');
        $request = $request->withMethod('POST');

        $response = $this->getMock(ResponseInterface::class);

        $next = function(ServerRequestInterface $request)  {
            $this->assertSame('PUT', $request->getMethod());
        };

        call_user_func($this->middleware, $request, $response, $next);
    }

    public function testNotOverride()
    {
        $request = new ServerRequest();
        $request = $request->withHeader(HttpMethodOverrideService::OVERRIDE_HEADER, 'GET');
        $request = $request->withMethod('POST');

        $response = $this->getMock(ResponseInterface::class);

        $next = function(ServerRequestInterface $request)  {
            $this->assertSame('POST', $request->getMethod());
        };

        call_user_func($this->middleware, $request, $response, $next);
    }
}