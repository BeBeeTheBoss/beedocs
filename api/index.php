<?php

if (getenv('VERCEL')) {
    $runtimePath = '/tmp/beedocs';

    foreach (['views', 'sessions', 'cache'] as $directory) {
        $path = $runtimePath.'/'.$directory;

        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }
    }

    $serverlessEnvironment = [
        'LOG_CHANNEL' => 'stderr',
        'VIEW_COMPILED_PATH' => $runtimePath.'/views',
        'SESSION_FILES_PATH' => $runtimePath.'/sessions',
    ];

    foreach ($serverlessEnvironment as $key => $value) {
        putenv($key.'='.$value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
