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

die("Bỏ single này, chạy song song...");

check_unique_script();

$loop = 0;
while (1) {
    $loop++;
    echo "\n===== NLOOP = $loop   . " . __FILE__;
    sleep(1);
    \App\Http\ControllerApi\EventInfoControllerApi::sendAllMessageLoop();
    sleep(10);
}
?>
