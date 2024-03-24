<?php

use Michnhokn\Logger;

return [
    'kirby-logger' => function () {
        return [
            'label' => t('michnhokn.logger.label'),
            'icon' => 'file-search',
            'menu' => true,
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
