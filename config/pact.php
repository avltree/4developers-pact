<?php declare(strict_types=1);

return [
    'host'          => env('PACT_HOST', 'localhost'),
    'port'          => env('PACT_PORT', 7200),
    'pact_dir'      => env('PACT_DIR', '/app/pacts'),
    'consumer_name' => env('PACT_CONSUMER', 'consumer'),
    'provider_name' => env('PACT_PROVIDER', 'provider'),
    'log_path'      => env('PACT_LOG_PATH', 'provider'),
];
