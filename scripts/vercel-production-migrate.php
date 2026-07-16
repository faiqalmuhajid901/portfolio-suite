<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Vercel Production Migration
|--------------------------------------------------------------------------
|
| File ini dijalankan oleh Composer saat dependency Laravel dipasang oleh
| vercel-php. Migration hanya berjalan pada deployment Production Vercel.
|
| Pada komputer lokal dan Preview Deployment, script langsung berhenti.
|
*/

$isVercel = getenv('VERCEL') === '1';

$isProduction = getenv('VERCEL_ENV') === 'production';

if (! $isVercel || ! $isProduction) {
    exit(0);
}

$projectRoot = dirname(__DIR__);

$artisanPath = $projectRoot . DIRECTORY_SEPARATOR . 'artisan';

if (! is_file($artisanPath)) {
    fwrite(
        STDERR,
        "File artisan tidak ditemukan: {$artisanPath}" . PHP_EOL
    );

    exit(1);
}

$phpBinary = escapeshellarg(PHP_BINARY);

$artisanFile = escapeshellarg($artisanPath);

$command = sprintf(
    '%s %s migrate --force --no-interaction',
    $phpBinary,
    $artisanFile
);

echo PHP_EOL;
echo 'Running Laravel production migrations...' . PHP_EOL;

passthru($command, $exitCode);

if ($exitCode !== 0) {
    fwrite(
        STDERR,
        'Laravel production migration failed.' . PHP_EOL
    );

    exit($exitCode);
}

echo 'Laravel production migrations completed.' . PHP_EOL;

exit(0);
