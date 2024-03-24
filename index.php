<?php

use Kirby\Cms\App;
use Kirby\Filesystem\F;

@include_once __DIR__ . '/vendor/autoload.php';
F::loadClasses(['Michnhokn\Logger' => __DIR__ . '/Logger.php']);

App::plugin('michnhokn/logger', [
    'icons' => [],
    'api' => require_once __DIR__ . "/config/api.php",
    'translations' => require_once __DIR__ . "/config/translations.php",
    'hooks' => require_once __DIR__ . "/config/hooks.php",
    'areas' => require_once __DIR__ . "/config/areas.php",
    'options' => [
        'channels' => [],
        'ignoreHooks' => [
            'page.render:after',
            'kirbytags:after'
        ]
    ]
]);
