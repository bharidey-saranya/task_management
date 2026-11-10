<?php

// 1. Prepare writable storage directory in /tmp for Vercel's read-only environment
$storagePath = '/tmp/storage';
foreach ([
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');

// 2. Fallback APP_KEY if not configured in Vercel dashboard
if (!getenv('APP_KEY')) {
    putenv('APP_KEY=base64:H8r47hW9qHhFwB3eKz0iQGv7c1N5mZ9s8yJ2rP1kLwA=');
    $_ENV['APP_KEY'] = 'base64:H8r47hW9qHhFwB3eKz0iQGv7c1N5mZ9s8yJ2rP1kLwA=';
    $_SERVER['APP_KEY'] = 'base64:H8r47hW9qHhFwB3eKz0iQGv7c1N5mZ9s8yJ2rP1kLwA=';
}

// 3. Fallback database if external MySQL host is not configured yet
if (!getenv('DB_HOST') && !getenv('DB_DATABASE')) {
    $sqlitePath = '/tmp/database.sqlite';
    if (!file_exists($sqlitePath)) {
        @touch($sqlitePath);
    }
    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=' . $sqlitePath);
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqlitePath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $sqlitePath;
}

// Forward request to Laravel public entrypoint
require __DIR__ . '/../public/index.php';
