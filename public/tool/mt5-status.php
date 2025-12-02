<?php


$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);


require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
outputT("/var/glx/weblog/mt5-status.log", "$ip OK");

echo "<br/>\n Update OK!";
