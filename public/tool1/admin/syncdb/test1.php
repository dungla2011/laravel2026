<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    require_once 'E:\\Projects\\laravel2022-01\\laravel01\\public\\index.php';
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

foreach ($rows as $m1) {
    $dbName = $m1['Database'];
    echo "<br/>\n $dbName";
}
