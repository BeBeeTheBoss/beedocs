<?php

// This is the serverless entry point. Vercel's application directory is
// read-only, so Laravel's complete storage tree (including its emergency
// logger) must be redirected before the framework is bootstrapped.
$temporaryPath = rtrim(sys_get_temp_dir(), '/').'/beedocs';
$storagePath = $temporaryPath.'/storage';
$bootstrapCachePath = $temporaryPath.'/bootstrap-cache';

foreach (['logs', 'framework/cache/data', 'framework/sessions', 'framework/views'] as $directory) {
    $path = $storagePath.'/'.$directory;

    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

if (! is_dir($bootstrapCachePath)) {
    mkdir($bootstrapCachePath, 0775, true);
}

$serverlessEnvironment = [
    'LARAVEL_STORAGE_PATH' => $storagePath,
    'LOG_CHANNEL' => 'stderr',
    'CACHE_DRIVER' => 'array',
    'SESSION_DRIVER' => 'cookie',
    'VIEW_COMPILED_PATH' => $storagePath.'/framework/views',
    'SESSION_FILES_PATH' => $storagePath.'/framework/sessions',
    'APP_CONFIG_CACHE' => $bootstrapCachePath.'/config.php',
    'APP_EVENTS_CACHE' => $bootstrapCachePath.'/events.php',
    'APP_PACKAGES_CACHE' => $bootstrapCachePath.'/packages.php',
    'APP_ROUTES_CACHE' => $bootstrapCachePath.'/routes-v7.php',
    'APP_SERVICES_CACHE' => $bootstrapCachePath.'/services.php',
];

foreach ($serverlessEnvironment as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
