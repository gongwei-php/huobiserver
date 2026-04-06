<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
return [
    'default' => [
        'host' => '127.0.0.1',
        'auth' => 'Ab111222..',
        'port' => 6379,
        'db' => 0,
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) env('REDIS_MAX_IDLE_TIME', 60),
        ],
    ],
    'jwt' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'auth' => 'Ab111222..',
        'port' => env('REDIS_PORT', 6379),
        'db' => env('REDIS_DB', 0),
    ],
];
