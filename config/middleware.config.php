<?php

return [
    'container' => [
        'factories' => [
            RstGroup\HttpMethodOverride\HttpMethodOverrideService::class => RstGroup\HttpMethodOverride\HttpMethodOverrideServiceFactory::class,
            RstGroup\HttpMethodOverride\HttpMethodOverrideMiddleware::class => RstGroup\HttpMethodOverride\HttpMethodOverrideMiddlewareFactory::class,
        ],
    ],
];