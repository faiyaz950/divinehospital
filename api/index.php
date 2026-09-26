<?php

/**
 * Vercel entry point. Only /tmp is writable on Vercel, so caches, compiled views and the
 * database live there, and every new instance starts from the database built during
 * deployment (see the "vercel" Composer script). APP_KEY comes from the project settings.
 */
$environment = [
    'VERCEL' => '1',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'VIEW_COMPILED_PATH' => '/tmp/views',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => '/tmp/database.sqlite',
    'CACHE_STORE' => 'database',
    'SESSION_DRIVER' => 'cookie',
    'SESSION_SECURE_COOKIE' => 'true',
    'QUEUE_CONNECTION' => 'sync',
    'LOG_CHANNEL' => 'stderr',
    'MAIL_MAILER' => 'log',
];

foreach ($environment as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $_SERVER[$key] = $value;
}

if (! is_dir($environment['VIEW_COMPILED_PATH'])) {
    mkdir($environment['VIEW_COMPILED_PATH'], 0755, true);
}

if (! file_exists($environment['DB_DATABASE'])) {
    copy(__DIR__.'/../database/vercel.sqlite', $environment['DB_DATABASE']);
}

require __DIR__.'/../public/index.php';
