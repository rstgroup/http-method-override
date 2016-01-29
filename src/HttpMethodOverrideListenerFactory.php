<?php

namespace RstGroup\HttpMethodOverride;

/**
 * @codeCoverageIgnore
 */
final class HttpMethodOverrideListenerFactory
{
    public function __invoke($services)
    {
        $service = $services->get(HttpMethodOverrideService::class);

        return new HttpMethodOverrideListener($service);
    }
}
