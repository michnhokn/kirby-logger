<?php

namespace Michnhokn;

use Exception;
use Kirby\Cms\Collection;
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

        if (!empty($filter['timestamp'])) {
            $query->where("timestamp >= '{$filter['timestamp']}'");
        }
        if (!empty($filter['oldSearch'])) {
            $query->where("oldData LIKE '%{$filter['oldSearch']}%'");
        }
        if (!empty($filter['newSearch'])) {
            $query->where("newData LIKE '%{$filter['newSearch']}%'");
        }
        if (!empty($filter['slug'])) {
            $query->where("slug LIKE '%{$filter['slug']}%'");
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
        if (!empty($filter['language'])) {
            $query->where(['language' => $filter['language']]);
        }

        $totalQuery = clone $query;

        $total = $totalQuery->count();
        /** @var Collection $result */
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
        if (!self::$connection->validateTable('logs')) {
            return 0;
        }

        return self::$connection
            ->table('logs')
            ->count();
    }

    /**
     * @param  string  $field
     * @return array
     */
    public static function options(string $field): array
    {
        if (!self::$connection->validateTable('logs')) {
            return [];
        }
        $results = self::$connection
            ->table('logs')
            ->select($field)
            ->distinct()
            ->fetch('array')
            ->all()
            ->data();

        return array_map(function ($result) {
            return [
                'value' => current($result),
                'text' => current($result),
            ];
        }, $results);
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
                $log->setSlug($event->newSite()->id());
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
                        $log->setSlug($event->newPage()->id());
                        $log->setOldData($event->oldPage()->num());
                        $log->setNewData($event->newPage()->num());

                        return $log;
                    case 'changeSlug':
                        $log->setSlug($event->newPage()->id());
                        $log->setOldData($event->oldPage()->slug());
                        $log->setNewData($event->newPage()->slug());

                        return $log;
                    case 'changeStatus':
                        $log->setSlug($event->newPage()->id());
                        $log->setOldData($event->oldPage()->status());
                        $log->setNewData($event->newPage()->status());

                        return $log;
                    case 'changeTemplate':
                        $log->setSlug($event->newPage()->id());
                        $log->setOldData($event->oldPage()->template()->name());
                        $log->setNewData($event->newPage()->template()->name());

                        return $log;
                    case 'changeTitle':
                        $log->setSlug($event->newPage()->id());
                        $log->setOldData($event->oldPage()->title()->toString());
                        $log->setNewData($event->newPage()->title()->toString());

                        return $log;
                    case 'create':
                        $log->setSlug($event->page()->id());
                        $log->setOldData();
                        $log->setNewData($event->page()->toArray());

                        return $log;
                    case 'delete':
                        $log->setSlug($event->page()->id());
                        $log->setOldData($event->page()->toArray());
                        $log->setNewData('x');

                        return $log;
                    case 'duplicate':
                        $log->setSlug($event->duplicatePage()->id());
                        $log->setOldData($event->originalPage()->toArray());
                        $log->setNewData($event->duplicatePage()->toArray());

                        return $log;
                    case 'update':
                        $log->setSlug($event->newPage()->id());
                        $log->setOldData($event->oldPage()->content()->toArray());
                        $log->setNewData($event->newPage()->content()->toArray());

                        return $log;
                }
                break;
            case 'file':
                switch ($action) {
                    case 'changeName':
                        $log->setSlug($event->newFile()->id());
                        $log->setOldData($event->oldFile()->title()->toString());
                        $log->setNewData($event->newFile()->title()->toString());

                        return $log;
                    case 'changeSort':
                        $log->setSlug($event->newFile()->id());
                        $log->setOldData($event->oldFile()->sort());
                        $log->setNewData($event->newFile()->sort());

                        return $log;
                    case 'create':
                        $log->setSlug($event->file()->id());
                        $log->setOldData();
                        $log->setNewData($event->file()->toArray());

                        return $log;
                    case 'delete':
                        $log->setSlug($event->file()->id());
                        $log->setOldData($event->file()->toArray());
                        $log->setNewData('x');

                        return $log;
                    case 'update':
                    case 'replace':
                        $log->setSlug($event->newFile()->id());
                        $log->setOldData($event->oldFile()->toArray());
                        $log->setNewData($event->newFile()->toArray());

                        return $log;
                }
                break;
            case 'user':
                switch ($action) {
                    case 'changeEmail':
                        $log->setSlug($event->newUser()->id());
                        $log->setOldData($event->oldUser()->email());
                        $log->setNewData($event->newUser()->email());

                        return $log;
                    case 'changeLanguage':
                        $log->setSlug($event->newUser()->id());
                        $log->setOldData($event->oldUser()->language());
                        $log->setNewData($event->newUser()->language());

                        return $log;
                    case 'changeName':
                        $log->setSlug($event->newUser()->id());
                        $log->setOldData($event->oldUser()->name()->toString());
                        $log->setNewData($event->newUser()->name()->toString());

                        return $log;
                    case 'changePassword':
                        $log->setSlug($event->newUser()->id());
                        $log->setOldData($event->oldUser()->password());
                        $log->setNewData('●●●●●●●●');

                        return $log;
                    case 'changeRole':
                        $log->setSlug($event->newUser()->id());
                        $log->setOldData($event->oldUser()->role()->name());
                        $log->setNewData($event->newUser()->role()->name());

                        return $log;
                    case 'create':
                        $log->setSlug($event->user()->id());
                        $log->setOldData('');
                        $log->setNewData($event->user()->toArray());

                        return $log;
                    case 'delete':
                        $log->setSlug($event->user()->id());
                        $log->setOldData($event->user()->toArray());
                        $log->setNewData('x');

                        return $log;
                    case 'update':
                        $log->setSlug($event->newUser()->id());
                        $log->setOldData($event->oldUser()->toArray());
                        $log->setNewData($event->newUser()->toArray());

                        return $log;
                    case 'login':
                        $log->setSlug($event->user()->id());
                        $log->setOldData();
                        $log->setNewData('-');

                        return $log;
                    case 'logout':
                        $log->setSlug($event->user()->id());
                        $log->setOldData();
                        $log->setNewData('x');

                        return $log;
                }
                break;
        }

        $log->setSlug('error');
        $log->setOldData('-');
        $log->setNewData('-');

        return $log;
    }
}
