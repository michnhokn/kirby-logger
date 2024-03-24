<?php

use Kirby\Cms\App;
use Kirby\Cms\Event;
use Michnhokn\Logger;

return [
    'system.exception' => function (Throwable $exception) {
        Logger::write(
            $exception->getMessage(),
            Logger::LEVEL_ERROR,
            Logger::CHANNEL_DEFAULT,
            [
                'userId' => App::instance()->user()?->id(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTrace()
            ],
        );
    },
    '*:after' => function (Event $event) {
        $types = ['file', 'page', 'user', 'site', 'language', 'kirbytags'];

        if (!in_array($event->type(), $types)) {
            return;
        }

        if (in_array($event->name(), option('michnhokn.logger.ignoreHooks', []))) {
            return;
        }

        $kirby = App::instance();

        Logger::write(
            $event->name(),
            Logger::LEVEL_INFO,
            Logger::CHANNEL_AUDIT,
            [
                'type' => $event->type(),
                'action' => $event->action(),
                'state' => $event->state(),
                'userId' => $kirby->user()?->id(),
                'language' => $kirby->language()->code()
            ],
        );
    },
];
