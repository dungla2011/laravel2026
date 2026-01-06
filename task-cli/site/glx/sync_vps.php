<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.lad.vn';

require __DIR__.'/../../../vendor/autoload.php';
$app = require_once __DIR__.'/../../../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!isCli()){
    die("Only run in CLI mode!");
}

// Get credentials from command line arguments or .env
$domain = $argv[1] ?? env('VCENTER_DOMAIN');
$uid = $argv[2] ?? env('VCENTER_UID');
$pw = $argv[3] ?? env('VCENTER_PW') ;
//$powerState = $argv[4] ?? 'POWERED_ON';
$powerState = null;
// Debug: show what we got
echo "Domain: $domain\n";
echo "UID: $uid\n";
//echo "Password length: " . strlen($pw) . "\n";
//echo "Power State: $powerState\n\n";

// Use Artisan to run the command with proper dependency injection
\Illuminate\Support\Facades\Artisan::call('vmware:sync-instances', [
    '--domain' => $domain,
    '--uid' => $uid,
    '--pw' => $pw,
//    '--power-state' => $powerState,
]);

echo \Illuminate\Support\Facades\Artisan::output();
