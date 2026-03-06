<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Helpers\BkavEHoaDonAPI;

//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$file = "/var/glx/weblog/myip1.log";

if($_GET['set'] ?? ''){
    $ip = $_SERVER['REMOTE_ADDR'];
    file_put_contents($file, $ip . "#" . nowyh());
    die();
}
if(file_exists($file))
    echo (file_get_contents($file));
