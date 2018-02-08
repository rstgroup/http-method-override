<?php

namespace RstGroup\HttpMethodOverride;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * @codeCoverageIgnore
 */
final class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        $config = require __DIR__ . '/../config/config.php';
        $moduleConfig = require __DIR__ . '/../config/module.config.php';

        return array_merge($config, $moduleConfig);
    }
}
