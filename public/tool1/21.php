<?php
$time = microtime(1);
use App\Models\User_Meta;

$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);


require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$dtime = microtime(1) - $time;
echo "\n DTIME = $dtime";

$user = \App\Models\User::find(1);

dump($user);
