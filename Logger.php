<?php

namespace Michnhokn;

use Kirby\Data\Json;
use Kirby\Database\Database;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Date;
use Kirby\Toolkit\Str;

class Logger
{
    public const LEVEL_DEBUG = 'DEBUG';

    public const LEVEL_INFO = 'INFO';

    public const LEVEL_WARNING = 'WARNING';

    public const LEVEL_ERROR = 'ERROR';

    public const LEVEL_CRITICAL = 'CRITICAL';

    private const LEVELS = [
        self::LEVEL_DEBUG,
        self::LEVEL_INFO,
        self::LEVEL_WARNING,
        self::LEVEL_ERROR,
        self::LEVEL_CRITICAL
    ];

    public const CHANNEL_DEFAULT = 'default';

    public const CHANNEL_AUDIT = 'audit';

    private const CHANNELS = [
        self::CHANNEL_DEFAULT,
        self::CHANNEL_AUDIT,
    ];

    private static ?Database $connection = null;

    public static function connect(): Database
    {
        if (static::$connection !== null) {
            return static::$connection;
        }

        static::$connection = new Database([
            'type' => 'sqlite',
            'database' => kirby()->root('logs') . "/logger.sqlite"
        ]);

        if (!self::$connection->validateTable('logs')) {
            self::$connection->execute(F::read(__DIR__ . '/logs-schema.sql'));
        }

        return self::$connection;
    }

    public static function write(
        string $message,
        string $level = self::LEVEL_INFO,
        string $channel = self::CHANNEL_DEFAULT,
        array $context = [],
        array $extra = [],
    ): void {
        if (!in_array($channel, self::getChannels())) {
            throw new \Exception("Logger channel $channel is not configured!");
        }
        (self::connect())->table('monolog')->insert([
            'channel' => $channel,
            'level' => $level,
            'message' => $message,
            'context' => Json::encode($context),
            'extra' => Json::encode($extra),
            'created_at' => Date::now()->format("Y-m-d\TH:i:s.uO"),
        ]);
    }


    public static function __callStatic(string $method, array $arguments)
    {
        $level = Str::upper($method);
        if (in_array($level, static::LEVELS)) {
            $arguments['level'] = $level;
            self::write(...$arguments);
            return;
        }

        throw new InvalidArgumentException('Invalid static Db method: ' . $method);
    }

    public static function getLevels(): array
    {
        return self::LEVELS;
    }

    public static function getChannels(): array
    {
        $channelsFromConfig = option('michnhokn.logger.channels', []);
        return A::merge(self::CHANNELS, $channelsFromConfig);
    }
}
