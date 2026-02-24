<?php

// Auto-migrate, seed, and sync from Turso on Vercel cold starts
if (getenv('APP_ENV') === 'production') {
    $dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);

        // Bootstrap Laravel to run migrations
        require __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->call('migrate', ['--force' => true]);

        // Sync persistent data from Turso → local SQLite FIRST
        \App\Services\TursoSync::syncToLocal();

        // Only seed admin if no users exist yet (first-ever deploy)
        if (\App\Models\User::count() === 0) {
            $kernel->call('db:seed', [
                '--class' => 'Database\\Seeders\\AdminSeeder',
                '--force' => true,
            ]);
        }

        // Clear the singleton so Laravel boots fresh for the actual request
        $app->flush();
        unset($app, $kernel);

        // Re-require bootstrap for the actual request below
        require __DIR__ . '/../vendor/autoload.php';
    }
}

require __DIR__ . '/../public/index.php';
