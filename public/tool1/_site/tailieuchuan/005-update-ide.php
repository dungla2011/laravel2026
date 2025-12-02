<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'tailieuchuan.net';

require "/var/www/html/public/index.php";

$mm = \App\Models\FileUpload::all();


die("stop...");

foreach ($mm AS $obj) {

    $ide = getUUidGlx();
    if(!$obj->ide__ || !str_contains($obj->ide__, '-')){
        $obj->ide__ = $ide;
        $obj->addLog("Update ide__: $ide");
        $obj->save();
        echo "\n<br> $obj->id. $obj->name  | $obj->ide__";
    }





}


