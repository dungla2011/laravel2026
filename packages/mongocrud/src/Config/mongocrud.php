<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MongoDB CRUD Configuration
    |--------------------------------------------------------------------------
    */

    // MongoDB connection name (from database.php)
    'connection' => env('MONGOCRUD_CONNECTION', 'mongodb'),

    // Default collection prefix
    'collection_prefix' => env('MONGOCRUD_PREFIX', ''),

    // API route prefix
    'route_prefix' => env('MONGOCRUD_ROUTE_PREFIX', 'api/mongo-crud'),

    // Web route prefix
    'web_route_prefix' => env('MONGOCRUD_WEB_ROUTE_PREFIX', 'mongo-crud'),

    // Enable/disable API routes
    'enable_routes' => env('MONGOCRUD_ENABLE_ROUTES', true),

    // Pagination settings
    'pagination' => [
        'per_page' => env('MONGOCRUD_PER_PAGE', 20),
        'max_per_page' => env('MONGOCRUD_MAX_PER_PAGE', 100),
    ],

    // Security settings
    'security' => [
        'enable_auth' => env('MONGOCRUD_ENABLE_AUTH', false),
        'middleware' => env('MONGOCRUD_MIDDLEWARE', []),
        'web_middleware' => env('MONGOCRUD_WEB_MIDDLEWARE', ['web']),
    ],
]; 