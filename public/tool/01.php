<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../vendor/autoload.php';

$app = require_once '../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


