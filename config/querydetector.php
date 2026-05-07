<?php

use App\Models\Shop;
use App\Models\User;

return [
    'enabled' => env('QUERY_DETECTOR_ENABLED', null),

    'threshold' => (int) env('QUERY_DETECTOR_THRESHOLD', 1),

    // Whitelist relations that are intentionally accessed in shared request helpers/layouts.
    'except' => [
        User::class => [
            Shop::class,
            'shop',
        ],
    ],

    'log_channel' => env('QUERY_DETECTOR_LOG_CHANNEL', 'daily'),

    'output' => [
        \BeyondCode\QueryDetector\Outputs\Alert::class,
        \BeyondCode\QueryDetector\Outputs\Log::class,
    ],
];
