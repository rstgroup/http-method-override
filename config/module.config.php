<?php

return [
    'service_manager' => [
        'factories' => [
            RstGroup\HttpMethodOverride\HttpMethodOverrideService::class => RstGroup\HttpMethodOverride\HttpMethodOverrideServiceFactory::class,
            RstGroup\HttpMethodOverride\HttpMethodOverrideListener::class => RstGroup\HttpMethodOverride\HttpMethodOverrideListenerFactory::class,
        ],
    ],
    'listeners' => [
        RstGroup\HttpMethodOverride\HttpMethodOverrideListener::class,
    ],
];
