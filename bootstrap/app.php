<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

//\//ladDebug::addTime("app___ ...", __LINE__);

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// Load .env early so env() works in domain_config.php
try {
    if (class_exists(\Dotenv\Dotenv::class)) {
        \Dotenv\Dotenv::createImmutable($app->environmentPath(), $app->environmentFile())->safeLoad();
    }
} catch (\Throwable $e) {
    // ignore if dotenv not available or .env missing
}

require __DIR__.'/domain_config.php';

if (0) {
    if (isset($_SERVER['HTTP_HOST']) &&
        ! empty($_SERVER['HTTP_HOST']) &&
        $_SERVER['HTTP_HOST'] == 'example.com') {
        try {
            $envStaging = '.env.example.com';
            $app->loadEnvironmentFrom($envStaging);
            (new \Dotenv\Dotenv(
                $app->environmentPath(),
                $app->environmentFile())
            )->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            // No custom .env file found for this domain
            // Add any additional code if needed to handle this scenario
        }
    }
}

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

//ladDebug::addTime("app___", __LINE__);

//require_once __DIR__."/../app/common.php";

//2024 Bo môngo
//Ket noi mongo db:
//require_once __DIR__."/../app/condb.php";

//ladDebug::addTime("app___ ...", __LINE__);

//Dang ky module de export migrate
// $app->register(\KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);

return $app;
