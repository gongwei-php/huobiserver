# component-creater
基于 [spatie/laravel-cors](https://github.com/spatie/laravel-cors),感谢开发者提供这样方便的组件。

## install
```shell script
composer require "gioco-plus/hyperf-cors"
```

## publish config

```shell script

php bin/hyperf.php vendor:publish gioco-plus/hyperf-cors
```

## usage

```php
config/autoload/middlewares.php

return [
    'http' => [
        \GiocoPlus\Cors\Middleware\CorsMiddleware::class,
    ],
];

```