<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$domainX = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";

if (!isCli()) {
    die("Not cli!");
}


//$mm = \App\Models\User::where('id', '>', 1600000)->get();
$mm = \App\Models\User::all();

$cc = 0;
foreach ($mm AS $obj) {
    $cc++;
    echo "\n $obj->id, $obj->email";
    $obj->setRoleUserIfRoleNull();
    \App\Models\UserCloud::getOrCreateNewUserCloud($obj->id);

}
