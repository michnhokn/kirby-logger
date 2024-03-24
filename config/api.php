<?php

use Kirby\Cms\App;
use Kirby\Data\Json;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Michnhokn\Logger;

return [
    'routes' => function (App $kirby) {
        return [
            [
                'pattern' => 'logs.json',
                'method' => 'POST',
                'action' => function () use ($kirby) {
                    $logger = Logger::connect();
                    $term = $kirby->request()->get('term');
                    $page = (int)$kirby->request()->get('page', 1);
                    $limit = (int)$kirby->request()->get('limit', 20);
                    $logLevel = $kirby->request()->get('level');
                    $logChannel = $kirby->request()->get('channel');

                    $query = $logger->table('monolog')
                        ->select('*')
                        ->order('created_at DESC')
                        ->offset(($page - 1) * $limit)
                        ->limit($limit);

                    $countQuery = $logger->table('monolog');

                    if ($logLevel and is_array($logLevel)) {
                        $query->where('level', 'in', $logLevel);
                        $countQuery->where('level', 'in', $logLevel);
                    }

                    if ($logChannel and is_array($logChannel)) {
                        $query->where('channel', 'in', $logChannel);
                        $countQuery->where('channel', 'in', $logChannel);
                    }

                    if (!empty($term)) {
                        $term = trim(Str::esc($term));
                        $query->where('message', 'like', "%$term%");
                        $countQuery->where('message', 'like', "%$term%");
                    }

                    $lines = $query->all();

                    return [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $countQuery->count(),
                        'logs' => A::map($lines->data(), function ($log) {
                            $log->context = Json::decode($log->context);
                            $log->extra = Json::decode($log->extra);
                            return $log;
                        }),
                    ];
                }
            ],
        ];
    }
];
