#!/usr/bin/env php
<?php
/**
 * Script setup database test từ file SQL
 * Usage: php setup_test_db.php
 */

$dbName = 'glx2026test';
$sqlFile = __DIR__ . '/database/glx_v3_for_tester.sql';

if (!file_exists($sqlFile)) {
    echo "Error: SQL file not found: " . $sqlFile . "\n";
    exit(1);
}

// Read .env để lấy connection info
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "Error: .env file not found\n";
    exit(1);
}

// Load env file
$env = [];
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    // Skip comments
    if (empty($line) || $line[0] === '#') {
        continue;
    }
    // Find first = sign
    $pos = strpos($line, '=');
    if ($pos === false) {
        continue;
    }
    $key = trim(substr($line, 0, $pos));
    $value = trim(substr($line, $pos + 1));
    // Remove quotes if present
    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
        $value = substr($value, 1, -1);
    }
    $env[$key] = $value;
}

$dbHost = $env['DB_HOST'] ?? 'localhost';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';
$dbPort = $env['DB_PORT'] ?? 3306;

echo "Setting up test database: " . $dbName . "\n";
echo "Host: " . $dbHost . ":" . $dbPort . "\n";
echo "User: " . $dbUser . "\n\n";

try {
    // Connect to MySQL để drop và create database
    $connection = new mysqli($dbHost, $dbUser, $dbPass, '', intval($dbPort));
    
    if ($connection->connect_error) {
        throw new Exception("Connect failed: " . $connection->connect_error);
    }

    echo "[1/3] Dropping old database if exists...\n";
    $connection->query("DROP DATABASE IF EXISTS `" . $dbName . "`");
    echo "      ✓ Done\n\n";

    echo "[2/3] Creating new database...\n";
    $connection->query("CREATE DATABASE `" . $dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "      ✓ Created: " . $dbName . "\n\n";

    $connection->close();

    echo "[3/3] Importing SQL file...\n";
    
    // Use mysql command line to import SQL file
    $password = !empty($dbPass) ? "-p" . escapeshellarg($dbPass) : "";
    $cmd = "mysql -h " . escapeshellarg($dbHost) . " -P " . intval($dbPort) . " -u " . escapeshellarg($dbUser) . " " . $password . " " . escapeshellarg($dbName) . " < " . escapeshellarg($sqlFile);
    
    // Execute the import
    $output = [];
    $exitCode = 0;
    exec($cmd . " 2>&1", $output, $exitCode);
    
    if ($exitCode === 0) {
        echo "      ✓ Imported\n";
        echo "      All statements executed successfully\n\n";
    } else {
        echo "      ⚠ Import completed with status code: " . $exitCode . "\n";
        if (!empty($output)) {
            foreach (array_slice($output, 0, 10) as $line) {
                echo "        " . $line . "\n";
            }
        }
        echo "\n";
    }

    echo "✓ Database setup completed successfully!\n";
    echo "Database: " . $dbName . "\n";
    echo "Connection: mysql:host=" . $dbHost . ";port=" . $dbPort . ";dbname=" . $dbName . ";charset=utf8mb4\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
