<?php

namespace RstGroup\HttpMethodOverride;

use Psr\Container\ContainerInterface;

/**
 * @codeCoverageIgnore
 */
final class HttpMethodOverrideListenerFactory
{
    public function __invoke(ContainerInterface $services)
    {
        $service = $services->get(HttpMethodOverrideService::class);

        return new HttpMethodOverrideListener($service);
    }
}
