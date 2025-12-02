<?php

namespace YourCompany\MongoCrud;

use Illuminate\Support\ServiceProvider;

class MongoCrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register any services here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load API routes
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        
        // Load web routes
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/Views', 'mongocrud');

        // Publish config if needed
        $this->publishes([
            __DIR__.'/Config/mongocrud.php' => config_path('mongocrud.php'),
        ], 'config');

        // Publish views if needed
        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/mongocrud'),
        ], 'views');

        // Load config
        $this->mergeConfigFrom(__DIR__.'/Config/mongocrud.php', 'mongocrud');
    }
} 