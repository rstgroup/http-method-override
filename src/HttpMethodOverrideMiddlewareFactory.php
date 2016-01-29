<?php

namespace RstGroup\HttpMethodOverride;

/**
 * @codeCoverageIgnore
 */
final class HttpMethodOverrideMiddlewareFactory
{
    public function __invoke($services)
    {
        $service = $services->get(HttpMethodOverrideService::class);

        return new HttpMethodOverrideMiddleware($service);
    }
}
