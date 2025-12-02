<?php
$storage = "/share";
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $storage = "e:/1";
}

// IMPORTANT: Parse --domain BEFORE loading database config
// This allows database.php to detect the correct connection
$parsedDomain = '';
if (php_sapi_name() === 'cli' && isset($_SERVER['argv'])) {
    foreach ($_SERVER['argv'] as $arg) {
        if (strpos($arg, '--domain=') === 0) {
            $parsedDomain = substr($arg, strlen('--domain='));
            $_SERVER['HTTP_HOST'] = $parsedDomain;
            $_SERVER['SERVER_NAME'] = $parsedDomain;
            break;
        }
    }
}



return [

    'backup' => [
        //'name' => env('APP_NAME', 'laravel-backup'),
        'name' => $parsedDomain ? gethostname() . '_' . $parsedDomain : gethostname(),
        'source' => [
            'files' => [
                'include' => filterExistingPaths([
                    base_path('artisan'), // File nhỏ để tránh lỗi "no files"
                    // Glob patterns - match nhiều files cùng lúc
                    '/bin/glx*', // Tất cả files bắt đầu bằng glx trong /bin
                    // Hoặc list cụ thể từng file
                    '/var/spool/cron/crontabs',
                    '/etc',
                    $uploadFolder = env('UPLOAD_FOLDER')
,                    // base_path(), // Uncomment nếu muốn backup toàn bộ project
                ]),
                'exclude' => [
                    base_path('vendor'),
                    base_path('storage'),
                    base_path('node_modules'),
                    base_path('.git'), // Exclude .git folder
                    // Add other folders to exclude here
                ],
                'follow_links' => false,
                'ignore_unreadable_directories' => false,
                'relative_path' => null,
                'direct_copy' => [ // New configuration for direct copy
//                    base_path('storage'),
//                    base_path('public/uploads'),
                ],
            ],
            // Tự động detect database connection dựa trên CLI arguments hoặc default
            'databases' => (function() {
                // Nếu đang chạy backup command từ CLI
                if (php_sapi_name() === 'cli' && isset($_SERVER['argv'])) {
                    $argv = $_SERVER['argv'];

                    // Parse --connection option
                    foreach ($argv as $arg) {
                        if (strpos($arg, '--connection=') === 0) {
                            $connection = substr($arg, strlen('--connection='));
                            return [$connection];
                        }
                    }

                    // Parse --domain option để auto-detect connection
                    foreach ($argv as $arg) {
                        if (strpos($arg, '--domain=') === 0) {
                            // Force reload database config để detect connection đúng
                            // Require database.php trực tiếp để đảm bảo config được load
                            $dbConfig = require __DIR__ . '/database.php';
                            $defaultConnection = $dbConfig['default'] ?? 'mysql';

                            return [$defaultConnection];
                        }
                    }

                    // Nếu đang chạy backup:run command, dùng default connection
                    if (in_array('backup:run', $argv)) {
                        $defaultConnection = config('database.default', 'mysql');
                        return [$defaultConnection];
                    }
                }

                // Default: không backup database nếu không chạy backup command
                return [];
            })(),
        ],

        'database_dump_compressor' => null,
        'database_dump_file_timestamp_format' => null,
        'database_dump_filename_base' => 'database',
        'database_dump_file_extension' => '',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level' => 9,
            'filename_prefix' => $parsedDomain ? $parsedDomain . '_' : '',
            'disks' => [
                'ftp'
            ],
        ],

        'temporary_directory' => sys_get_temp_dir() . '/backup-temp',
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => 'default',
        'tries' => 1,
        'retry_delay' => 0,
    ],

    'monitor_backups1' => [
        [
            'name' => env('APP_NAME', 'laravel-backup'),
            'disks' => ['local'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
        'tries' => 1,
        'retry_delay' => 0,
    ],

];
