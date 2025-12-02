<?php

namespace YourCompany\ServiceManager;

use Illuminate\Support\ServiceProvider;
use YourCompany\ServiceManager\Services\BillingService;
use YourCompany\ServiceManager\Services\ResourceCalculatorService;
use YourCompany\ServiceManager\Services\ServiceProvisioningService;

class ServiceManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BillingService::class);
        $this->app->singleton(ResourceCalculatorService::class);
        $this->app->singleton(ServiceProvisioningService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/Config/servicemanager.php' => config_path('servicemanager.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/Migrations/' => database_path('migrations'),
        ], 'migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/Views/' => resource_path('views/vendor/servicemanager'),
        ], 'views');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/Views', 'servicemanager');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }
} 