<?php

namespace RstGroup\HttpMethodOverride;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HttpMethodOverrideMiddleware
{
    private $service;

    public function __construct(HttpMethodOverrideService $service)
    {
        $this->service = $service;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return $response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $method = $request->getMethod();
        $headers = $this->getHeadersFromRequest($request);

        $overridedHeader = $this->service->getOverridedMethod($method, $headers);

        if ($method !== $overridedHeader) {
            $request = $request->withMethod($overridedHeader);
        }

        return $next($request, $response);
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