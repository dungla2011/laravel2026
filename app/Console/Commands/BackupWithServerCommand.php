<?php

namespace App\Console\Commands;

use Exception;
use Spatie\Backup\Commands\BackupCommand;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class BackupWithServerCommand extends BackupCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--filename=} {--only-db} {--db-name=*} {--only-files} {--only-to-disk=} {--disable-notifications} {--timeout=} {--tries=} {--domain=} {--connection=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the backup with optional domain hostname and database connection override.';

    public function __construct(Config $config)
    {
        // Parse command line options BEFORE parent constructor
        $this->parseEarlyOptions();
        
        parent::__construct($config);
    }
    
    /**
     * Parse --domain and --connection options early (before parent constructor)
     */
    protected function parseEarlyOptions(): void
    {
        // Parse argv để lấy options sớm
        $argv = $_SERVER['argv'] ?? [];
        
        $domainName = null;
        $connectionName = null;
        
        foreach ($argv as $arg) {
            if (strpos($arg, '--domain=') === 0) {
                $domainName = substr($arg, strlen('--domain='));
            } elseif (strpos($arg, '--connection=') === 0) {
                $connectionName = substr($arg, strlen('--connection='));
            }
        }
        
        // Set domain hostname
        if ($domainName) {
            $_SERVER['HTTP_HOST'] = $domainName;
            $_SERVER['SERVER_NAME'] = $domainName;
        }
        
        // Set database connection for backup
        if ($connectionName) {
            config(['backup.backup.source.databases' => [$connectionName]]);
        } elseif ($domainName) {
            // Nếu có domain nhưng không có connection, dùng default connection
            $defaultConnection = config('database.default');
            config(['backup.backup.source.databases' => [$defaultConnection]]);
        }
    }

    public function handle(): int
    {
        // Display info about what we're backing up
        $domainName = $this->option('domain');
        $connectionToBackup = $this->option('connection') ?? config('database.default');
        
        if ($domainName) {
            $this->comment("Domain hostname set to: {$domainName}");
        }
        
        // Show database connection info
        $databases = config('backup.backup.source.databases');
        if (!empty($databases)) {
            $connectionToBackup = $databases[0];
            
            // Lấy thông tin connection
            $connectionConfig = config("database.connections.{$connectionToBackup}");
            if ($connectionConfig && isset($connectionConfig['driver'])) {
                $driver = $connectionConfig['driver'];
                $database = $connectionConfig['database'] ?? 'unknown';
                
                if ($driver === 'pgsql') {
                    $schema = $connectionConfig['schema'] ?? 'public';
                    $this->comment("Database connection: {$connectionToBackup} (PostgreSQL: {$database}, schema: {$schema})");
                } else {
                    $this->comment("Database connection: {$connectionToBackup} ({$driver}: {$database})");
                }
            } else {
                $this->comment("Database connection set to: {$connectionToBackup}");
            }
        }

        // Call parent handle to execute backup
        return parent::handle();
    }
}
