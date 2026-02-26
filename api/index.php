<?php

// Ensure compiled views exist directory
if (!is_dir('/tmp/storage/framework/views')) {
    @mkdir('/tmp/storage/framework/views', 0755, true);
}

// Ensure Laravel clears older views on boot
if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('view:clear');
    $kernel->call('cache:clear');
}

require __DIR__ . '/../public/index.php';
