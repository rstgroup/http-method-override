<?php

namespace RstGroup\HttpMethodOverride;

/**
 * @codeCoverageIgnore
 */
final class HttpMethodOverrideServiceFactory
{
    public function __invoke($services)
    {
        $config = $services->get('config')['rst_group']['http_method_override'];

        return new HttpMethodOverrideService($config['map'], $config['override_headers']);
    }
}
