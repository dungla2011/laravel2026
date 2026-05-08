<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
use App\Models\SiteMng;
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


echo dfh1b('35514058425b5147414151404345564f57');
