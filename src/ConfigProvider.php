<?php

namespace RstGroup\HttpMethodOverride;

/**
 * @codeCoverageIgnore
 */
final class ConfigProvider
{
    public function __invoke()
    {
        $config = require __DIR__ . '/../config/config.php';
        $moduleConfig = require __DIR__ . '/../config/middleware.config.php';

        return array_merge($config, $moduleConfig);
    }
}