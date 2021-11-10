<?php

use Kirby\Cms\Event;
use Kirby\Database\Database;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

class Logger
{
    const TYPES = ['file', 'page', 'user', 'site'];

    /**
     * The singleton Database object
     *
     * @var Database
     */
    public static $connection = null;

    /**
     * Connect the database
     *
     * @return Database
     * @throws Exception
     */
    public static function connect(): ?Database
    {
        if (static::$connection !== null) {
            return static::$connection;
        }
        $logDir = kirby()->root('logs') . '/kirby3-logger/';
        $schema = F::read(__DIR__ . '/../logs-schema.sql');

        Dir::make($logDir, true);

        static::$connection = new Database(['type' => 'sqlite', 'database' => $logDir . 'logs.sqlite']);
        if (!self::$connection->validateTable('logs')) {
            self::$connection->execute($schema);
        }

        return self::$connection;
    }

    public static function logs()
    {
        return self::$connection->table('logs')->select('*')->all();
    }

    public static function log(Event $event)
    {
        if (!in_array($event->type(), self::TYPES)) {
            return;
        }

        $args = self::getArguments($event);
        self::$connection->table('logs')->insert([
            'type' => $event->type(),
            'action' => $event->action(),
            'user' => kirby()->user()->email(),
            'old' => $args[0] ?? '-',
            'new' => $args[1] ?? '-',
        ]);
    }

    private static function getArguments(Event $event)
    {
        switch ($event->type()) {
            case 'site':
                switch ($event->action()) {
                    case 'changeTitle':
                        return [$event->oldSite()->title(), $event->newSite()->title()];
                }
                break;
        }
    }
}
