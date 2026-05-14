<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
define("DEF_TOOL_CMS", 1);
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

require_once "/var/www/html/public/index.php";

if(!isSupperAdmin_()){
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!preg_match('/^103\.163\.216\./', $ip)) {
        die("Cannot access: IP $ip not allowed");
    }
}

$limit = 100;
$skip  = (int) ($_GET['skip'] ?? 0);

$m1 = \App\Models\User::orderBy('created_at')->skip($skip)->limit($limit)->get();

echo json_encode($m1->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
