<?php
/**
 * Laravel Database Configuration Inspector
 * Use Laravel DB facade to show current database configuration
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<br>=== Laravel Database Configuration ===\n";
echo "<br>Server: " . ($_SERVER['HTTP_HOST'] ?? 'CLI') . "\n";
echo "<br>Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Show default connection
    echo "<br>1. Default Database Connection:\n";
    $default = DB::getDefaultConnection();
    echo "<br>   Default connection name: $default\n";
    
    // 2. Get connection config
    echo "<br>\n2. Connection Configuration:\n";
    $config = config('database.connections.' . $default);
    echo "<br>   Driver: " . ($config['driver'] ?? 'not set') . "\n";
    echo "<br>   Host: " . ($config['host'] ?? 'not set') . "\n";
    echo "<br>   Port: " . ($config['port'] ?? 'not set') . "\n";
    echo "<br>   Database: " . ($config['database'] ?? 'not set') . "\n";
    echo "<br>   Username: " . ($config['username'] ?? 'not set') . "\n";
    echo "<br>   Charset: " . ($config['charset'] ?? 'not set') . "\n";
    echo "<br>   Collation: " . ($config['collation'] ?? 'not set') . "\n";
    
    // 3. Test connection and get server info
    echo "<br>\n3. Database Server Information:\n";
    $start_time = microtime(true);
    
    // Test connection
    $pdo = DB::connection()->getPdo();
    $connection_time = (microtime(true) - $start_time) * 1000;
    echo "<br>   Connection: ✓ SUCCESS\n";
    echo "<br>   Connection time: " . round($connection_time, 2) . " ms\n";
    
    // Get MySQL version
    $version = DB::select('SELECT VERSION() as version')[0]->version;
    echo "<br>   MySQL Version: $version\n";
    
    // 4. Test query performance
    echo "<br>\n4. Query Performance Test:\n";
    $query_start = microtime(true);
    $result = DB::select('SELECT 1 as test');
    $query_time = (microtime(true) - $query_start) * 1000;
    echo "<br>   Simple query time: " . round($query_time, 2) . " ms\n";
    
    // 5. Show MySQL variables
    echo "<br>\n5. Important MySQL Variables:\n";
    $variables = DB::select("
        SHOW VARIABLES WHERE Variable_name IN (
            'max_connections',
            'innodb_buffer_pool_size', 
            'wait_timeout',
            'interactive_timeout',
            'query_cache_size',
            'innodb_flush_log_at_trx_commit'
        )
    ");
    
    foreach ($variables as $var) {
        echo "<br>   {$var->Variable_name}: {$var->Value}\n";
    }
    
    // 6. Show current database status
    echo "<br>\n6. Database Status:\n";
    $status = DB::select("
        SHOW STATUS WHERE Variable_name IN (
            'Connections',
            'Queries', 
            'Uptime',
            'Threads_connected',
            'Threads_running'
        )
    ");
    
    foreach ($status as $stat) {
        echo "<br>   {$stat->Variable_name}: {$stat->Value}\n";
    }
    
    // 7. Show all available connections
    echo "<br>\n7. All Configured Connections:\n";
    $all_connections = config('database.connections');
    foreach ($all_connections as $name => $conn) {
        echo "<br>   $name: {$conn['driver']}://{$conn['host']}:{$conn['port']}/{$conn['database']}\n";
    }
    
    // 8. Domain-based database mapping (if exists)
    echo "<br>\n8. Domain-based Database Mapping:\n";
    $current_domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
    echo "<br>   Current domain: $current_domain\n";
    
    if (isset($GLOBALS['mMapDomainDb'])) {
        $mapping = $GLOBALS['mMapDomainDb'];
        if (isset($mapping[$current_domain])) {
            $domain_config = $mapping[$current_domain];
            echo "<br>   Mapped to:\n";
            echo "<br>     - Site ID: " . ($domain_config['siteid'] ?? 'N/A') . "\n";
            echo "<br>     - Database: " . ($domain_config['db_name'] ?? 'N/A') . "\n";
            echo "<br>     - Layout: " . ($domain_config['layout_name'] ?? 'N/A') . "\n";
        } else {
            echo "<br>   Domain not found in mapping\n";
        }
    } else {
        echo "<br>   No domain mapping configured\n";
    }
    
    // 9. Performance summary
    echo "<br>\n9. Performance Summary:\n";
    echo "<br>   Total script time: " . round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2) . " ms\n";
    echo "<br>   Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB\n";
    echo "<br>   Peak memory: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB\n";
    
} catch (Exception $e) {
    echo "<br>ERROR: " . $e->getMessage() . "\n";
    echo "<br>Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "<br>\n=== End Database Configuration Check ===\n";
?>