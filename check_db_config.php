<?php
/**
 * Database Connection Configuration Checker
 * Check and compare database settings between servers
 */

echo "=== Database Configuration Checker ===\n";
echo "Server: " . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'CLI') . "\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Check Laravel Environment
echo "1. Laravel Environment Variables:\n";
$env_vars = [
    'APP_ENV',
    'DB_CONNECTION', 
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD'
];

foreach ($env_vars as $var) {
    $value = env($var);
    if ($var === 'DB_PASSWORD') {
        $value = $value ? str_repeat('*', strlen($value)) : 'null';
    }
    echo "   $var = " . ($value ?? 'null') . "\n";
}

echo "\n";

// 2. Check Domain-based Database Mapping
echo "2. Domain-based Database Configuration:\n";
$current_domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
echo "   Current domain: $current_domain\n";

// Load domain mapping from config
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
    
    if (isset($GLOBALS['mMapDomainDb'])) {
        $domain_config = $GLOBALS['mMapDomainDb'];
        
        if (isset($domain_config[$current_domain])) {
            $config = $domain_config[$current_domain];
            echo "   Domain found in mapping:\n";
            echo "     - Site ID: " . ($config['siteid'] ?? 'not set') . "\n";
            echo "     - Database: " . ($config['db_name'] ?? 'not set') . "\n";
            echo "     - Layout: " . ($config['layout_name'] ?? 'not set') . "\n";
        } else {
            echo "   Domain NOT found in mapping\n";
            echo "   Available domains:\n";
            foreach (array_keys($domain_config) as $domain) {
                echo "     - $domain\n";
            }
        }
    } else {
        echo "   No domain mapping found\n";
    }
} else {
    echo "   Config file not found\n";
}

echo "\n";

// 3. Test Database Connection
echo "3. Database Connection Test:\n";
try {
    // Try to bootstrap Laravel
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        
        if (file_exists(__DIR__ . '/bootstrap/app.php')) {
            $app = require_once __DIR__ . '/bootstrap/app.php';
            
            // Get database configuration
            $db_config = config('database');
            $default_connection = $db_config['default'] ?? 'mysql';
            $connection_config = $db_config['connections'][$default_connection] ?? [];
            
            echo "   Default connection: $default_connection\n";
            echo "   Host: " . ($connection_config['host'] ?? 'not set') . "\n";
            echo "   Port: " . ($connection_config['port'] ?? 'not set') . "\n";
            echo "   Database: " . ($connection_config['database'] ?? 'not set') . "\n";
            echo "   Username: " . ($connection_config['username'] ?? 'not set') . "\n";
            
            // Test connection
            $start_time = microtime(true);
            
            try {
                DB::connection()->getPdo();
                $connection_time = microtime(true) - $start_time;
                echo "   Connection: ✓ SUCCESS\n";
                echo "   Connection time: " . round($connection_time * 1000, 2) . " ms\n";
                
                // Test a simple query
                $query_start = microtime(true);
                $result = DB::select('SELECT 1 as test');
                $query_time = microtime(true) - $query_start;
                
                echo "   Query test: ✓ SUCCESS\n";
                echo "   Query time: " . round($query_time * 1000, 2) . " ms\n";
                
            } catch (Exception $e) {
                echo "   Connection: ✗ FAILED\n";
                echo "   Error: " . $e->getMessage() . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   Laravel bootstrap failed: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Server Information
echo "4. Server Information:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "   Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "   Script Path: " . __FILE__ . "\n";

// 5. Performance Metrics
echo "\n5. Performance Metrics:\n";
$memory_usage = memory_get_usage(true);
$peak_memory = memory_get_peak_usage(true);
echo "   Memory Usage: " . round($memory_usage / 1024 / 1024, 2) . " MB\n";
echo "   Peak Memory: " . round($peak_memory / 1024 / 1024, 2) . " MB\n";

// 6. Check MySQL/MariaDB version if possible
echo "\n6. Database Server Info:\n";
try {
    if (class_exists('DB')) {
        $db_version = DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown';
        echo "   Database Version: $db_version\n";
        
        // Check connection variables
        $variables = DB::select("SHOW VARIABLES WHERE Variable_name IN ('max_connections', 'innodb_buffer_pool_size', 'query_cache_size')");
        foreach ($variables as $var) {
            echo "   {$var->Variable_name}: {$var->Value}\n";
        }
    }
} catch (Exception $e) {
    echo "   Could not retrieve database info: " . $e->getMessage() . "\n";
}

echo "\n=== End Database Configuration Check ===\n";
?>