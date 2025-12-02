#!/bin/bash

echo "=== Database Configuration Checker ==="
echo "Server: $(hostname)"
echo "Date: $(date)"
echo ""

echo "1. Checking server domain and PHP info..."
echo "Current domain: $HTTP_HOST"
php -r "echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;"
echo ""

echo "2. Laravel Environment Variables:"
cd /var/www/html
if [ -f .env ]; then
    echo "   .env file found"
    grep -E "^(APP_ENV|DB_)" .env | while IFS= read -r line; do
        if [[ $line == *"DB_PASSWORD"* ]]; then
            echo "   DB_PASSWORD=***hidden***"
        else
            echo "   $line"
        fi
    done
else
    echo "   .env file not found"
fi
echo ""

echo "3. Testing database connection speed..."
# Create a simple PHP script to test DB connection
php -r "
\$start = microtime(true);
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    
    \$pdo = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';port=' . env('DB_PORT', '3306') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        [PDO::ATTR_TIMEOUT => 5]
    );
    
    \$connection_time = microtime(true) - \$start;
    echo 'Connection: SUCCESS' . PHP_EOL;
    echo 'Connection time: ' . round(\$connection_time * 1000, 2) . ' ms' . PHP_EOL;
    
    // Test query
    \$query_start = microtime(true);
    \$stmt = \$pdo->query('SELECT 1');
    \$query_time = microtime(true) - \$query_start;
    echo 'Query time: ' . round(\$query_time * 1000, 2) . ' ms' . PHP_EOL;
    
    // Get MySQL version
    \$stmt = \$pdo->query('SELECT VERSION() as version');
    \$version = \$stmt->fetch(PDO::FETCH_ASSOC);
    echo 'MySQL Version: ' . \$version['version'] . PHP_EOL;
    
} catch (Exception \$e) {
    echo 'Connection: FAILED' . PHP_EOL;
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"
echo ""

echo "4. Domain-based database mapping check..."
php -r "
\$domain = \$_SERVER['HTTP_HOST'] ?? 'localhost';
echo 'Current domain: ' . \$domain . PHP_EOL;

if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    if (isset(\$GLOBALS['mMapDomainDb'])) {
        \$mapping = \$GLOBALS['mMapDomainDb'];
        if (isset(\$mapping[\$domain])) {
            \$config = \$mapping[\$domain];
            echo 'Domain mapping found:' . PHP_EOL;
            echo '  Site ID: ' . (\$config['siteid'] ?? 'not set') . PHP_EOL;
            echo '  Database: ' . (\$config['db_name'] ?? 'not set') . PHP_EOL;
            echo '  Layout: ' . (\$config['layout_name'] ?? 'not set') . PHP_EOL;
        } else {
            echo 'Domain NOT found in mapping' . PHP_EOL;
            echo 'Available domains: ' . implode(', ', array_keys(\$mapping)) . PHP_EOL;
        }
    }
}
"
echo ""

echo "5. MySQL connection variables..."
mysql -u$(grep DB_USERNAME .env | cut -d'=' -f2) -p$(grep DB_PASSWORD .env | cut -d'=' -f2) -h$(grep DB_HOST .env | cut -d'=' -f2) -e "SHOW VARIABLES WHERE Variable_name IN ('max_connections', 'innodb_buffer_pool_size', 'wait_timeout', 'interactive_timeout');" 2>/dev/null || echo "Could not connect to MySQL directly"

echo ""
echo "6. Network latency to database server..."
DB_HOST=$(grep DB_HOST .env | cut -d'=' -f2)
if [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Testing ping to $DB_HOST..."
    ping -c 3 $DB_HOST 2>/dev/null || echo "Could not ping database server"
else
    echo "Database is on localhost"
fi

echo ""
echo "=== Database Check Complete ==="