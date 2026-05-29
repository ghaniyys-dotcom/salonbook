<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),
    'release' => env('SENTRY_RELEASE'),
    'environment' => env('APP_ENV', 'production'),
    'breadcrumbs' => [
        'logs' => true,
        'sql_queries' => true,
        'sql_bindings' => true,
        'queue_info' => true,
        'command_info' => true,
    ],
    'traces_sample_rate' => (float)(env('SENTRY_TRACES_SAMPLE_RATE', 0.2)),
    'profiles_sample_rate' => (float)(env('SENTRY_PROFILES_SAMPLE_RATE', 0.0)),
    'send_default_pii' => true,
    'ignore_exceptions' => [
        \Illuminate\Http\Exceptions\NotFoundHttpException::class,
    ],
];
