<?php

use Michnhokn\Logger;

return [
    'kirby-logger' => function ($kirby) {
        $user = $kirby->user();

        if ($user === null) {
            return false;
        }
    
        $permissions = $user->role()->permissions();
    
        return [
            'label' => t('michnhokn.logger.label'),
            'icon' => 'file-search',
            'menu' => $permissions->for('michnhokn.logger', 'access'),
            'link' => 'logger',
            'views' => [
                [
                    'pattern' => 'logger',
                    'action' => function () {
                        return [
                            'component' => 'k-logger-area',
                            'title' => t('michnhokn.logger.logs'),
                            'props' => [
                                'channels' => Logger::getChannels(),
                                'levels' => Logger::getLevels()
                            ]
                        ];
                    },
                ],
            ],
        ];
    },
];
