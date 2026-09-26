<?php

/**
 * Vercel entry point. Only /tmp is writable on Vercel, so every new instance
 * starts from the database built during deployment (see the "vercel" Composer script).
 */
$database = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';

if (! file_exists($database)) {
    copy(__DIR__.'/../database/vercel.sqlite', $database);
}

require __DIR__.'/../public/index.php';
