<?php

declare(strict_types=1);

return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
            BASE_PATH . '/app/Http/Api',
            BASE_PATH . '/kernel',
        ],
        'collectors' => [],
        'ignore_annotations' => [],
    ],
    'include_functions' => [
        BASE_PATH . '/app/Common/functions.php',
    ],
];
