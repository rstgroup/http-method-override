# http-method-override [![Build Status](https://travis-ci.org/rstgroup/http-method-override.svg?branch=master)](https://travis-ci.org/rstgroup/http-method-override)
HTTP method override service

Library allow to override method by header. Why? Because some clients cannot send custom method. It can be used as
PSR-7 middeware or Zend Framework 2 module.

## Installation

```json
{
    "require": {
        "rstgroup/http-method-override": "dev-master"
    }
}
```

You need to configure how it's possible to override methods. To do that you need to create/modify `config` service in
your dependecy container:

```php
return [
    'rst_group' => [
        'http_method_override' => [
            'map' => [
                'POST' => ['LINK', 'PUT'],
            ],
            'override_headers' => [],
        ],
    ],
];
```

It will add ability to use POST method as LINK or PUT. You can override it using given request:

```
POST http://example.com/page
X-HTTP-Method-Override: PUT
```

`override_headers` allow you to define own header to override.

### Specific installation for PSR-7 middeware

Use [Expressive Configuration Manager](https://github.com/mtymek/expressive-config-manager) to add library config.
After this you can enable middleware in your middleware-stack using `RstGroup\HttpMethodOverride\HttpMethodOverrideMiddleware`
service name in your container.

### Specific installation for Zend Framework 2

Add module `RstGroup\HttpMethodOverride` to `application.config.php` file.
