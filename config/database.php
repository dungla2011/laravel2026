<?php

use Illuminate\Support\Str;

//if(isDebugIp())
//    $GLOBALS['mMapDomainDb']['tapdanhmay.mytree.vn'] = ['siteid' => 26, 'db_name' => 'glx_typing_train', 'layout_name' => 'tap-danh-may2'];
$dbConnection = env('DB_CONNECTION');
$dbName = env('DB_DATABASE');
$dbSchema = 'not_define';
//GRANT ALL ON glx2022db.* TO 'glx2022db_user'@'12.0.0.0/255.255.255.0' IDENTIFIED BY '...';
$hostname = env('DB_HOST', '127.0.0.1');

$dbPort = env('DB_PORT', '3306');

$domainName = \LadLib\Common\UrlHelper1::getDomainHostName();

if(isCli()){

    //DOMAIN_CLI
    $DOMAIN_CLI = getenv("DOMAIN_CLI") ?? '';
    if($DOMAIN_CLI){
        $domainName = $DOMAIN_CLI;
    }


    // Check if running backup:run command with --domain argument
    if (isset($_SERVER['argv'])) {
        $argv = $_SERVER['argv'];
        $isBackupCommand = false;

        // Check if command contains backup:run
        foreach ($argv as $arg) {
            if (strpos($arg, 'backup:run') !== false) {
                $isBackupCommand = true;
                break;
            }
        }

        // If backup:run command, look for --domain argument
        if ($isBackupCommand) {
            foreach ($argv as $arg) {
                if (strpos($arg, '--domain=') === 0) {
                    $domainName = substr($arg, strlen('--domain='));
                    break;
                }
            }
        }
    }

}

$dbDriver = env('DB_DRIVER', 'mysql');

// Initialize variables
$user = null;
$pw = null;

//Nếu là localhost domain truy cập, và app url local, thì db local:
if ((isCli() && gethostname() == 'DESKTOP-VFQHFQS')
    ||
    ($domainName == 'localhost' && env('APP_URL') == 'http://localhost:8000')
    ||
    ($domainName == '127.0.0.1' && env('APP_URL') == 'http://localhost:8000')
) {
    // $hostname = '127.0.0.1';
    // $user = 'root';
    // $pw = '';

    // die("1111");

    $hostname = 'localhost';
    $dbDriver = 'sqlite';
    $dbConnection = 'sqlite';
    $dbDriver = 'mysql';
    $dbConnection = 'mysql';
    $dbName = 'glx2026test';
    $user = 'root';
    $pw = '';
    $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = 'localhost';

} else

{

    //VPN, để testing có thể kết nối vào 12.0.0.1
    $hostname = env('DB_HOST_VPN');
    $user = env('DB_USERNAME_DEFAULT');
    $pw = env('DB_PASSWORD_DEFAULT');

    if (isset($GLOBALS['mMapDomainDb'][$domainName])) {

        $dbName = $GLOBALS['mMapDomainDb'][$domainName]['db_name'];

        if(str_starts_with($dbName, 'DB_PG2')){

            $dbDriver = 'pgsql';
            $dbName = env('DB_PG2_DATABASE');
            $hostname = env('DB_PG2_HOST');
            $user = env('DB_PG2_USERNAME');
            $pw = env('DB_PG2_PASSWORD');
            $dbSchema = env('DB_PG2_SCHEMA');
            $dbPort = env('DB_PG2_PORT', '5432');


//            die(" DB PG1_DATABASE: " . $dbName . " - " . $hostname . " - " . $user . " - " . $pw . " - " . $dbPort);
        }
        else
        if(str_starts_with($dbName, 'DB_PG1')){
            $dbDriver = 'pgsql';
            $dbName = env('DB_PG1_DATABASE');
            $hostname = env('DB_PG1_HOST');
            $user = env('DB_PG1_USERNAME');
            $pw = env('DB_PG1_PASSWORD');
            $dbSchema = env('DB_PG1_SCHEMA');
            $dbPort = env('DB_PG1_PORT', '5432');
//            die(" DB PG1_DATABASE: " . $dbName . " - " . $hostname . " - " . $user . " - " . $pw . " - " . $dbPort);
        }
        else
        if(str_starts_with($dbName, 'DB_RM_HOST-')){

            $num = explode('-', $dbName)[1];
            $hostname = env('DB_RM_HOST' . $num);
            $dbName = env('DB_RM_NAME' . $num);
//            $dbName0 = $dbName = env('DB_RM_NAME' . $num);
            $user = env('DB_RM_USER' . $num);
            $pw = env('DB_RM_PW' . $num);
        }
        else {
            $user = env('DB_USERNAME_DEFAULT');
            $pw = env('DB_PASSWORD_DEFAULT');
            $hostname = env('DB_HOST_VPN');
            if($tmpHost = $GLOBALS['mMapDomainDb'][$domainName]['db_host'] ?? ''){
                $hostname = $tmpHost;
            }
        }

        $GLOBALS['GLX_SITE_ID'] = $GLOBALS['mMapDomainDb']['siteid'] ?? 0;

    } else {
        $GLOBALS['GLX_SITE_ID'] = 0;
        if (! isCli()) {
            exit('Not valid domain gx');
        }
        $dbName = env('DB_DATABASE_GLX_TESTING');
    }

    //Fortester:
    //    if(!isCli())
    //Trường hợp ko set domain name, và là pc001 thì là tester:
    if ((gethostname() == 'DESKTOP-VFQHFQS')) {


        //GRANT ALL ON `glx_%`.* TO 'for_sync'@'12.0.0.%' IDENTIFIED BY '...';
        //GRANT ALL ON glx2023_for_testing.* TO 'tester1'@'12.0.0.%' IDENTIFIED BY '...';
        //GRANT ALL ON glx2022db.* TO 'tester1'@'12.0.0.%' IDENTIFIED BY '...';
        //FLUSH PRIVILEGES;
        $dbName = env('DB_DATABASE_GLX_TESTING');
        $user = env('DB_USERNAME_DEFAULT');
        $pw = env('DB_PASSWORD_DEFAULT');
        $hostname = env('DB_HOST_VPN');

        ///////////////////
        $dbName = env('DB_RM_NAME8');
        $user = env('DB_RM_USER8');
        $pw = env('DB_RM_PW8');
        $hostname = '12.0.0.24';
        $dbDriver = "common_connection";
        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = 'glx.lad.vn';

        $hostname = 'localhost';
        $dbDriver = 'sqlite';
        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = 'localhost';

//        die("Localhost....");

//        die("xxx1 . " . env('DB_HOST_VPN'));

        //        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = 'test2023.galaxycloud.vn';
    }
    if (gethostname() == 'events.ncbd') {
        $dbName = env('DB_RM_NAME2');
        $user = env('DB_RM_USER2');
        $pw = env('DB_RM_PW2');
        $hostname = env('DB_RM_HOST2');
    }
}

if (isDebugIp()) {
//    die("xxxxxx $domainName - $dbName - $hostname - $user - $pw - $dbPort - $dbDriver");
}


if(isCli()){
    if(gethostname() == 'mon.lad.vn'){
//        print_r($_SERVER['argv']);

//        die("ABC1 '$domainName': $dbName , $hostname, $user, $pw, $dbPort, $dbDriver");
    }
}

$common_connection = [
    'driver' => $dbDriver,
    'url' => env('DATABASE_URL'),
    'host' => $hostname,
    'port' => $dbPort,
    'database' => $dbName,
    'username' => $user,
    'password' => $pw,
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
];

if($dbDriver == 'pgsql') {

    $dbConnection = 'pgsql';


    $common_connection = [
        'driver' => $dbDriver,
        'url' => env('DATABASE_URL'),
        'host' => $hostname,
        'port' => $dbPort,
        'database' => $dbName,
        'username' => $user,
        'password' => $pw,
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => $dbSchema,
        'sslmode' => 'prefer',
    ];


}
if ((isCli() && gethostname() == 'DESKTOP-VFQHFQS')) {
//    echo "\n --- " . $_SERVER['SERVER_NAME'] ;
//    die("\n DB $dbDriver dbConnection = $dbConnection ; $dbName , $hostname, $user, $pw, $dbPort");
}
$db_info = [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => $dbConnection, //env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'mongodb' => [
            'driver' => 'mongodb',
            'dsn' => env('DB_URI', 'mongodb://127.0.0.1:27017/testdb1'),
            'database' => 'testdb1',
        ],

        'mongodb_testdb3' => [
            'driver' => 'mongodb',
            'dsn' => env('DB_URI_TESTDB3', 'mongodb://127.0.0.1:27017/testdb3'),
            'database' => 'testdb3',
        ],

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'common_connection' => $common_connection,

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $hostname,
            'port' => $dbPort,
            'database' => $dbName,
            'username' => $user,
            'password' => $pw,
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_for_dev' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_VPN'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE_GLX_TESTING'),
            'username' => env('DB_USERNAME_DEFAULT'),
            'password' => env('DB_PASSWORD_DEFAULT'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // Our secondary database connection
        'mysql_for_common' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $hostname,
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE'),
            'username' => $user,
            'password' => $pw,
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => $hostname,
            'port' => $dbPort,
//            'database' => env('DB_DATABASE', 'forge'),
//            'username' => env('DB_USERNAME', 'forge'),
//            'password' => env('DB_PASSWORD', ''),
            'database' => $dbName,
            'username' => $user,
            'password' => $pw,
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => $dbSchema,
            'sslmode' => 'prefer',
        ],

        'pgsql2' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_PG2_HOST'),
            'port' => env('DB_PG2_PORT', '5432'),
            'database' => env('DB_PG2_DATABASE'),
            'username' => env('DB_PG2_USERNAME'),
            'password' => env('DB_PG2_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => env('DB_PG2_SCHEMA', 'public'),
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),
//        'client' => env('REDIS_CLIENT', 'predis'),

//        'options' => [
//            'cluster' => env('REDIS_CLUSTER', 'redis'),
//            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
//        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];

//if(isDebugIp()){
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($db_info);
//    echo "</pre>";
//    die();
//}

return $db_info;
