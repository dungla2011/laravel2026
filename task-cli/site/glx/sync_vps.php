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

check_unique_script();

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

$cc = 0;
while(1){
    echo "\n⏱️  Checking sync task... (Loop #" . ($cc + 1) . ")\n";

    if($cc > 0)
        sleep(10);

    $cc++;

    // Backup vps_usages table every 10 minutes (every 60 loops of 10 seconds = 600 seconds)
    if (($cc - 1) % 300 == 0) {
        echo "\n🔄 Running hourly backup...\n";

        $backupDir = '/var/glx/weblog/backup_table';
        $zipFilename = 'vps_usages-' . date('Y-m-d-H') . '.zip';
        $zipFilePath = $backupDir . '/' . $zipFilename;

        if (file_exists($zipFilePath)) {
            echo "  ✓ Backup already exists for this hour: $zipFilename\n";
        } else {
            $result = \App\Models\VpsUsage::backupTable($zipFilePath);
            if ($result['success']) {
                $fileSizeKb = round($result['file_size'] / 1024, 2);
                echo "  ✅ {$result['message']} ($fileSizeKb KB)\n";
            } else {
                echo "  ❌ {$result['message']}\n";
            }
        }
    }

    // Use Artisan to run the command with proper dependency injection
    \Illuminate\Support\Facades\Artisan::call('vmware:sync-instances', [
        '--domain' => $domain,
        '--uid' => $uid,
        '--pw' => $pw,
    //    '--power-state' => $powerState,
    ]);
}
echo \Illuminate\Support\Facades\Artisan::output();
