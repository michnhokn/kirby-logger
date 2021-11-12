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
    'api' => [
        'routes' => [
            [
                'pattern' => 'logs.json',
                'method' => 'POST',
                'action' => function () {
                    return Logger::logs(
                        $this->requestBody('page', 1),
                        $this->requestBody('limit', 10),
                        $this->requestBody('filter', [])
                    );
                },
            ],
        ],
    ],
    'areas' => [
        'kirby3-logger' => function () {
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
                            ];
                        },
                    ],
                ],
            ];
        },
    ],
]);