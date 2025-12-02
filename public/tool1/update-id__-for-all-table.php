<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'tailieuchuan.net';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

require "/var/www/html/public/index.php";

if(!isCli()){
    die(" NOT CLI!");
}

use App\Models\FileUpload;

$mm = FileUpload::all();
$mm = \App\Models\GiaPha::all();
//$mm = \App\Models\MyTreeInfo::all();

$cc = 0;
$tt = count($mm);
foreach ($mm AS $obj){
    $cc++;
    echo "\n $cc/$tt, $obj->id, $obj->name";
    if(!$obj->id__)
        $obj->save();
}
