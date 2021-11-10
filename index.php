<?php

require_once __DIR__ . '/src/Logger.php';

\Kirby\Cms\App::plugin('michnhokn/logger', [
    'hooks' => [
        'system.loadPlugins:after' => function () {
            Logger::connect();
        },
        '*:after' => function (\Kirby\Cms\Event $event) {
            Logger::log($event);
        },
    ],
    'areas' => [
        'kirby3-logger' => function ($kirby) {
            return [
                'label' => 'Logger',
                'icon' => 'table',
                'menu' => true,
                'link' => 'logger',
                'views' => [
                    [
                        'pattern' => 'logger',
                        'action' => function () {
                            return [
                                'component' => 'k-logger-area',
                                'title' => 'Logs',
                                'props' => [
                                    'logs' => Logger::logs(),
                                ],
                            ];
                        },
                    ],
                ],
            ];
        },
    ],
]);