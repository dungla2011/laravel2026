<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/var/www/html/public/index.php';

if (isset($_GET['get123'])) {
    $mm = \App\Models\ModelMetaInfo::all()->toArray();
    echo serialize($mm);
}
