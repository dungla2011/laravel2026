<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

if(str_contains(gethostname(), 'mytree'))
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'ncbd.mytree.vn';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
check_unique_script();

//Lấy tham số eventId và type = sms/email từ argv
$eventId = $argv[1] ?? 0;
$type = $argv[2] ?? 'sms';

echo "\n eventId = $eventId, type = $type";

//for ($i = 0; $i < 10; $i++) {
//    echo "$eventId , Loop $i\n";
//    sleep(1);
//}

\App\Http\ControllerApi\EventInfoControllerApi::sendAllMessageLoop($eventId, $type);

