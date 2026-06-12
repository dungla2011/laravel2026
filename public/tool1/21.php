<?php
////$time = microtime(1);
////use App\Models\User_Meta;
////
////$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
////
////
//$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "v2up.4share.vn";
//
////require "/var/www/html/vendor/autoload.php";
////$app = require_once '/var/www/html/bootstrap/app.php';
////$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
////$response = $kernel->handle(
////    $request = Illuminate\Http\Request::capture()
////);
////
////
//require "/var/www/html/public/index.php";
echo "<br/>\n xxx =1 ";
echo file_get_contents("https://sv18.4share.vn/tool/sysinfo_glx.php?checkfile=2f6d6e742f7364312f363530343030302d363530353030302f36353034303337223433363532303922");
return;

// === DEBUG: kiểm tra config cache ===
echo "<pre> === DEBUG cache & bootstrap ===\n";
$cachePath = base_path('bootstrap/cache/config.php');
echo "Config cached file: " . $cachePath . "\n";
echo "Config is cached:   " . (file_exists($cachePath) ? 'YES ← đây là nguyên nhân!' : 'NO') . "\n";
echo "PHP SAPI:           " . php_sapi_name() . "\n";
echo "mMapDomainDb set:   " . (isset($GLOBALS['mMapDomainDb']) ? 'YES (' . count($GLOBALS['mMapDomainDb']) . ' domains)' : 'NO ← chưa load domain_config') . "\n";
echo "</pre>";

// === DEBUG: config database.php đã build ra gì ===
echo "<pre> === DEBUG config('database.connections.mysql') ===\n";
$mysqlCfg = config('database.connections.mysql');
echo "host:     " . $mysqlCfg['host'] . "\n";
echo "database: " . $mysqlCfg['database'] . "\n";
echo "username: " . $mysqlCfg['username'] . "\n";
echo "default:  " . config('database.default') . "\n";
echo "</pre>";

$db = \Illuminate\Support\Facades\DB::connection();

// Cách 1: từ config Laravel
$config = $db->getConfig();
echo "<pre> === Cách 1: getConfig() ===\n";
echo "Driver:    " . $config['driver'] . "\n";
echo "Host:      " . ($config['host'] ?? 'N/A') . "\n";
echo "Port:      " . ($config['port'] ?? 'N/A') . "\n";
echo "Database:  " . $config['database'] . "\n";
echo "Username:  " . $config['username'] . "\n";
echo "Charset:   " . ($config['charset'] ?? 'N/A') . "\n";
echo "Collation: " . ($config['collation'] ?? 'N/A') . "\n";
echo "</pre>";

// Cách 2: query trực tiếp từ MySQL server
$rows = \Illuminate\Support\Facades\DB::select("
    SELECT
        @@hostname        AS server_hostname,
        @@port            AS server_port,
        DATABASE()        AS current_database,
        USER()            AS `current_user`,
        VERSION()         AS mysql_version,
        @@character_set_server AS charset,
        @@collation_server     AS collation
");
echo "<pre> === Cách 2: MySQL system variables ===\n";
print_r($rows[0]);
echo "</pre>";

// Cách 2b: dump env vars thực tế trên server
echo "<pre> === Cách 2b: ENV vars DB_RM_HOST-1 trên server ===\n";
echo "DB_RM_HOST1:  " . env('DB_RM_HOST1') . "\n";
echo "DB_RM_NAME1:  " . env('DB_RM_NAME1') . "\n";
echo "DB_RM_USER1:  " . env('DB_RM_USER1') . "\n";
echo "SERVER_NAME:  " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "\n";
echo "gethostname(): " . gethostname() . "\n";
echo "APP_ENV:      " . env('APP_ENV') . "\n";
echo "mMapDomainDb[v2up]: ";
print_r($GLOBALS['mMapDomainDb']['v2up.4share.vn'] ?? 'NOT FOUND');
echo "\n</pre>";

// User::find(1)
$user = \App\Models\User::find(1);
echo "<pre> === User::find(1) ===\n";
echo "DB query chạy trên: " . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "\n";
print_r($user ? $user->toArray() : 'NOT FOUND');
echo "</pre>";

// Cách 3: PDO connection info
$pdo = $db->getPdo();
echo "<pre> === Cách 3: PDO ===\n";
echo "Driver:         " . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) . "\n";
echo "Server version: " . $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION) . "\n";
echo "Server info:    " . $pdo->getAttribute(\PDO::ATTR_SERVER_INFO) . "\n";
echo "Client version: " . $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION) . "\n";
echo "Connection status: " . $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS) . "\n";
echo "</pre>";
