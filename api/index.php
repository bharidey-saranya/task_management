<?php

// Prepare writable storage directory in /tmp for Vercel's read-only serverless environment
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

// Forward request to Laravel public entrypoint
require __DIR__ . '/../public/index.php';
