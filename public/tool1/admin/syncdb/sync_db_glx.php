#!/usr/bin/env php
<?php

class clsConfigTmpSyncDbGlx
{
    public static $ignore_drop_table = [
        'don_vi_hanh_chinhs',
        'model_meta_infos',
        'rand_table',
    ];

    //Áp dụng thay đổi cho tất cả các bảng còn lại nếu diff sql tương tự
    public static $apply_for_all_last_if_the_same = '';
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

//    require_once 'E:\\Projects\\laravel2022-01\\laravel01\\public\\index.php';
    require 'E:\\Projects\\laravel2022-01\\laravel01\\vendor/autoload.php';
    $app = require_once 'E:\\Projects\\laravel2022-01\\laravel01/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

} else {
    require_once '/var/www/html/public/index.php';
}

if (! isCli()) {
    exit('NOT CLI!');
}

$con = \Illuminate\Support\Facades\DB::getPdo();

if ($con instanceof PDO);
$stm = $con->query('SHOW DATABASES');
$stm->setFetchMode(PDO::FETCH_ASSOC);
$rows = $stm->fetchAll();

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($rows);
//echo "</pre>";
//

$paths = [
    __DIR__.'/../../autoload.php',
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../../autoload.php',
    __DIR__.'/lib/vendor/autoload.php',
];
foreach ($paths as $file) {
    if (file_exists($file)) {
        $vendor = $file;
        break;
    }
}
unset($paths, $file);

if (! isset($vendor)) {
    exit("Could not find composer install.\n");
}

require_once $vendor;

class clsTmpConfSyncDb
{
    public $host;

    public $user;

    public $password;

    public $database;

    public $port;
}

$src = new clsTmpConfSyncDb();
$src->host = 'glx1254.abc';
$src->user = 'for_sync';
$src->password = 'Qaz@12abc_000';
$src->database = 'glx2023_for_testing';
$src->port = '3306';

$dst = new clsTmpConfSyncDb();
$dst->host = 'glx1254.abc';
$dst->user = 'for_sync';
$dst->password = 'Qaz@12abc_000';
$dst->database = 'glx00_gp_tpl';
$dst->port = '3306';

$mm = ['source' => $src, 'dest' => $dst];
//mysql_sync($mm);

//echo json_encode($mm, JSON_PRETTY_PRINT);

foreach ($rows as $m1) {

    $dbName = $m1['Database'];

    if ($dbName == $src->database) {
        echo "<br/>\n Ignore src: $dbName";

        continue;
    }

    if (! starts_with($dbName, 'glx')) {
        continue;
    }
    echo "<br/>\n ************************** $dbName ***************************";

    //    continue;

    $dst = new clsTmpConfSyncDb();
    $dst->host = 'glx1254.abc';
    $dst->user = 'for_sync';
    $dst->password = 'Qaz@12abc_000';
    $dst->database = $dbName;
    $dst->port = '3306';
    $mm = ['source' => $src, 'dest' => $dst];

    try{

        mysql_sync($mm);
    }
    catch (Throwable $e) { // For PHP 7
        echo "<br/>\n $dbName Error1: ".$e->getMessage();
        getch("...");
    }
    catch (Exception $exception){
        echo "<br/>\n $dbName Error2: ".$exception->getMessage();
        getch("...");
    }

}
