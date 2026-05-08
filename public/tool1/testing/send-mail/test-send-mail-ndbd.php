<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<br/>\n LINE = ".__LINE__;

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';
echo "<br/>\n LINE = ".__LINE__;

require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


sendMailNcbd("dungla2011@gmail.com", "Mời họp lần 2" ,
    " Kính mời ông bà đến dự buổi họp tổng kết dự án NCBD tại phòng họp tầng 3!", null, 1);


echo "<br/>\n LINE = ".__LINE__;

echo "<br/>\n Test mail1";
