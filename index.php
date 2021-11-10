<?php

\Kirby\Cms\App::plugin('michnhokn/logger', [
    'hooks' => [
        '*:before' => function (\Kirby\Cms\Event $event) {
            $type = $event->type();
            $action = $event->action();
        }
    ],
    'routes' => [
        [
            'pattern' => 'db',
            'action' => function () {
                $logDir = kirby()->root('logs') . '/kirby3-logger/';
                \Kirby\Filesystem\Dir::make($logDir, true);
                $database = new \Kirby\Database\Database([
                    'type' => 'sqlite',
                    'database' => $logDir . 'logs.sqlite'
                ]);
                $schema = \Kirby\Filesystem\F::read(__DIR__ . '/logs-schema.sql');
                $database->execute($schema);
                dump(
                    $database->table('logs')->insert([
                        'type' => 'test',
                        'action' => 'action',
                        'userId' => 'userId',
                        'oldValue' => 'oldValue',
                        'currentValue' => 'currentValue',
                    ])
                );
                die;
            }
        ],
    ],
    'areas' => [
        'kirby3-logger' => function ($kirby) {
            $logDir = kirby()->root('logs') . '/kirby3-logger/';
            $database = new \Kirby\Database\Database([
                'type' => 'sqlite',
                'database' => $logDir . 'logs.sqlite'
            ]);

            return [
                'label' => 'Logger',
                'icon' => 'table',
                'menu' => true,
                'link' => 'logger',
                'views' => [
                    [
                        'pattern' => 'logger',
                        'action' => function () use ($database) {
                            return [
                                'component' => 'k-logger-view',
                                'title' => 'Logs',
                                'props' => [
                                    'logs' => $database->table('logs')->all()
                                ]
                            ];
                        }
                    ]
                ]
            ];
        }
    ]
]);