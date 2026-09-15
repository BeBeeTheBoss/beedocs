<?php

// This is the serverless entry point. Vercel's application directory is
// read-only, so Laravel's complete storage tree (including its emergency
// logger) must be redirected before the framework is bootstrapped.
$storagePath = rtrim(sys_get_temp_dir(), '/').'/beedocs-storage';

foreach (['logs', 'framework/cache/data', 'framework/sessions', 'framework/views'] as $directory) {
    $path = $storagePath.'/'.$directory;

    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

$serverlessEnvironment = [
    'LARAVEL_STORAGE_PATH' => $storagePath,
    'LOG_CHANNEL' => 'stderr',
    'VIEW_COMPILED_PATH' => $storagePath.'/framework/views',
    'SESSION_FILES_PATH' => $storagePath.'/framework/sessions',
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
