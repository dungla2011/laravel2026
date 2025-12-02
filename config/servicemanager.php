<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MongoDB Connection
    |--------------------------------------------------------------------------
    */
    'mongodb' => [
        'connection' => env('SERVICEMANAGER_MONGODB_CONNECTION', 'mongodb'),
        'database' => env('SERVICEMANAGER_MONGODB_DATABASE', 'service_manager'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Configuration
    |--------------------------------------------------------------------------
    */
    'billing' => [
        'currency' => env('SERVICEMANAGER_CURRENCY', 'VND'),
        'decimal_places' => env('SERVICEMANAGER_DECIMAL_PLACES', 2),
        'billing_cycle_check_interval' => env('SERVICEMANAGER_BILLING_CYCLE_CHECK', 60), // seconds
        'auto_suspend_on_insufficient_funds' => env('SERVICEMANAGER_AUTO_SUSPEND', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Types Configuration
    |--------------------------------------------------------------------------
    */
    'resource_types' => [
        'cpu' => [
            'name' => 'CPU Core',
            'unit' => 'core',
            'billing_units' => ['minute', 'hour', 'day', 'month'],
        ],
        'ram' => [
            'name' => 'RAM',
            'unit' => 'GB',
            'billing_units' => ['minute', 'hour', 'day', 'month'],
        ],
        'disk' => [
            'name' => 'Disk Space',
            'unit' => 'GB',
            'billing_units' => ['minute', 'hour', 'day', 'month'],
        ],
        'network' => [
            'name' => 'Network Bandwidth',
            'unit' => 'Mbps',
            'billing_units' => ['minute', 'hour', 'day', 'month'],
        ],
        'ip' => [
            'name' => 'IP Address',
            'unit' => 'ip',
            'billing_units' => ['hour', 'day', 'month'],
        ],
        'database' => [
            'name' => 'Database',
            'unit' => 'db',
            'billing_units' => ['day', 'month'],
        ],
        'domain' => [
            'name' => 'Domain',
            'unit' => 'domain',
            'billing_units' => ['month', 'year'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Status
    |--------------------------------------------------------------------------
    */
    'service_status' => [
        'active' => 'Active',
        'suspended' => 'Suspended',
        'terminated' => 'Terminated',
        'pending' => 'Pending',
        'provisioning' => 'Provisioning',
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Periods
    |--------------------------------------------------------------------------
    */
    'billing_periods' => [
        'minute' => 1,
        'hour' => 60,
        'day' => 1440,
        'month' => 43200, // 30 days
        'year' => 525600, // 365 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'manage_services' => 'Manage Services',
        'manage_plans' => 'Manage Service Plans',
        'manage_billing' => 'Manage Billing',
        'view_reports' => 'View Reports',
    ],
]; 