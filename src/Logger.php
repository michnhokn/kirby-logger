<?php

require_once __DIR__ . '/Log.php';

use Kirby\Cms\Event;
use Kirby\Database\Database;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

class Logger
{
    const TYPES = ['file', 'page', 'user', 'site', 'languages'];

    /**
     * The singleton Database object
     *
     * @var Database|null
     */
    public static ?Database $connection = null;

    /**
     * Connect the database
     *
     * @return Database|null
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

    /**
     * @param  int  $page
     * @param  int  $limit
     * @param  array  $filter
     * @return array
     */
    public static function logs(int $page = 1, int $limit = 10, array $filter = []): array
    {
        if (!self::$connection->validateTable('logs')) {
            return [];
        }

        $query = self::$connection->table('logs')->select('*');

        if (!empty($filter['oldSearch'])) {
            $query->where("oldData LIKE '%{$filter['oldSearch']}%'");
        }
        if (!empty($filter['newSearch'])) {
            $query->where("newData LIKE '%{$filter['newSearch']}%'");
        }
        if (!empty($filter['action'])) {
            $query->where(['action' => $filter['action']]);
        }
        if (!empty($filter['type'])) {
            $query->where(['type' => $filter['type']]);
        }
        if (!empty($filter['user'])) {
            $query->where(['user' => $filter['user']]);
        }
        if (!empty($filter['slug'])) {
            $query->where(['slug' => $filter['slug']]);
        }
        if (!empty($filter['language'])) {
            $query->where(['language' => $filter['language']]);
        }

        $totalQuery = clone $query;

        $total = $totalQuery->count();
        /** @var \Kirby\Cms\Collection $result */
        $result = $query->page($page, $limit);

        return [
            'logs' => $result ? array_values($result->toArray()) : [],
            'total' => $total ?? 0,
        ];
    }

    /**
     * @return int
     */
    public static function total(): int
    {
        return self::$connection->table('logs')->count();
    }

    /**
     * @param  Event  $event
     */
    public static function log(Event $event): void
    {
        if (!in_array($event->type(), self::TYPES)) {
            return;
        }

        $log = self::getLog($event);
        self::$connection->table('logs')->insert($log->toArray());
    }

    /**
     * @param  Event  $event
     * @return Log
     */
    private static function getLog(Event $event): Log
    {
        $log = new Log([
            'type' => $event->type(),
            'action' => $event->action(),
            'user' => kirby()->user()->email(),
            'language' => kirby()->languageCode(),
        ]);
        $type = $event->type();
        $action = $event->action();

        switch ($type) {
            case 'site':
                $log->setSlug('site');
                switch ($action) {
                    case 'changeTitle':
                        $log->setOldData($event->oldSite()->title()->toString());
                        $log->setNewData($event->newSite()->title()->toString());

                        return $log;
                    case 'update':
                        $log->setOldData($event->oldSite()->content()->toArray());
                        $log->setNewData($event->newSite()->content()->toArray());

                        return $log;
                }
                break;
            case 'page':
                switch ($action) {
                    case 'changeNum':
                        $log->setSlug($event->newPage()->slug());
                        $log->setOldData($event->oldPage()->num());
                        $log->setNewData($event->newPage()->num());

                        return $log;
                    case 'changeSlug':
                        $log->setSlug($event->newPage()->slug());
                        $log->setOldData($event->oldPage()->slug());
                        $log->setNewData($event->newPage()->slug());

                        return $log;
                    case 'changeStatus':
                        $log->setSlug($event->newPage()->slug());
                        $log->setOldData($event->oldPage()->status());
                        $log->setNewData($event->newPage()->status());

                        return $log;
                    case 'changeTemplate':
                        $log->setSlug($event->newPage()->slug());
                        $log->setOldData($event->oldPage()->template()->name());
                        $log->setNewData($event->newPage()->template()->name());

                        return $log;
                    case 'changeTitle':
                        $log->setSlug($event->newPage()->slug());
                        $log->setOldData($event->oldPage()->title()->toString());
                        $log->setNewData($event->newPage()->title()->toString());

                        return $log;
                    case 'create':
                        $log->setSlug($event->page()->slug());
                        $log->setOldData();
                        $log->setNewData($event->page()->toArray());

                        return $log;
                    case 'delete':
                        $log->setSlug($event->page()->slug());
                        $log->setOldData($event->page()->toArray());
                        $log->setNewData('x');

                        return $log;
                    case 'duplicate':
                        $log->setSlug($event->duplicatePage()->slug());
                        $log->setOldData($event->originalPage()->toArray());
                        $log->setNewData($event->duplicatePage()->toArray());

                        return $log;
                    case 'update':
                        $log->setSlug($event->newPage()->slug());
                        $log->setOldData($event->oldPage()->content()->toArray());
                        $log->setNewData($event->newPage()->content()->toArray());

                        return $log;
                }
        }

        $log->setSlug('error');
        $log->setOldData('-');
        $log->setNewData('-');

        return $log;
    }
}
