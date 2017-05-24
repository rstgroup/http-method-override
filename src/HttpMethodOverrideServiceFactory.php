<?php

namespace RstGroup\HttpMethodOverride;

use Psr\Container\ContainerInterface;

/**
 * @codeCoverageIgnore
 */
final class HttpMethodOverrideServiceFactory
{
    public function __invoke(ContainerInterface $services)
    {
        $config = $services->get('config')['rst_group']['http_method_override'];

        return new HttpMethodOverrideService($config['map'], $config['override_headers']);
    }
}
