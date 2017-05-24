<?php

namespace RstGroup\HttpMethodOverride;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use PhpMiddleware\DoublePassCompatibilityTrait;
use Psr\Http\Message\ServerRequestInterface;

final class HttpMethodOverrideMiddleware implements MiddlewareInterface
{
    use DoublePassCompatibilityTrait;

    private $service;

    public function __construct(HttpMethodOverrideService $service)
    {
        $this->service = $service;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $method = $request->getMethod();
        $headers = $this->getHeadersFromRequest($request);

        $overridedHeader = $this->service->getOverridedMethod($method, $headers);

        if ($method !== $overridedHeader) {
            $request = $request
                ->withMethod($overridedHeader)
                ->withAttribute('overrided-method', $method);
        }
        return $delegate->process($request);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    private function getHeadersFromRequest(ServerRequestInterface $request)
    {
        $headerNames = [];

        foreach ($this->service->getOverrideHeaders() as $headerName) {
            if ($request->hasHeader($headerName)) {
                $headerNames[$headerName] = $request->getHeaderLine($headerName);
            }
        }
        return $headerNames;
    }
}
