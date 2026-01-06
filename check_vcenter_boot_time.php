<?php
/**
 * Test script to find boot time endpoint in vCenter
 * Run: php check_vcenter_boot_time.php
 */

//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


// Config từ env (giống như trong SyncVmwareInstancesCommand)
$domain = env('VCENTER_DOMAIN');
$uid = env('VCENTER_UID');
$pw = env('VCENTER_PW');
$vmId = $argv[1] ?? env('VMWARE_VM_ID', 'vm-102818'); // Có thể truyền qua command line

if (!$domain || !$uid || !$pw) {
    echo "❌ vCenter credentials required!\n";
    echo "Set env variables: VCENTER_DOMAIN, VCENTER_UID, VCENTER_PW\n";
    echo "Or pass VM ID: php check_vcenter_boot_time.php vm-102818\n";
    exit(1);
}

echo "🔐 Logging into vCenter...\n";

// Login
$ch = curl_init("https://$domain/rest/com/vmware/cis/session");
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "");
curl_setopt($ch, CURLOPT_USERPWD, $uid . ':' . $pw);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$ret = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Login failed (HTTP $httpCode): $ret\n";
    exit(1);
}

$out = json_decode($ret, true);
$sid = $out['value'] ?? null;

if (!$sid) {
    echo "❌ No session ID\n";
    exit(1);
}

echo "✓ Logged in. SID: " . substr($sid, 0, 20) . "...\n\n";

// Test endpoints
$endpoints = [
    "/rest/vcenter/vm/{vmId}/power" => "Power state endpoint",
    "/rest/vcenter/vm/{vmId}" => "VM info endpoint",
    "/rest/vcenter/vm/{vmId}/hardware" => "Hardware endpoint",
    "/rest/vcenter/vm/{vmId}/tools" => "Tools endpoint",
    "/rest/vcenter/vm/{vmId}/guest/processes" => "Guest processes endpoint",
];

foreach ($endpoints as $endpoint => $description) {
    $url = str_replace('{vmId}', $vmId, $endpoint);
    $fullUrl = "https://$domain" . $url;

    echo "📍 Testing: $description\n";
    echo "   URL: $url\n";

    $ch = curl_init($fullUrl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["vmware-api-session-id: $sid"]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        echo "   ✓ HTTP 200 OK\n";
        $data = json_decode($response, true);

        // Print response to see structure
        echo "   Response: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

        // Check for boot time related fields
        $jsonStr = json_encode($data);
        if (stripos($jsonStr, 'boot') !== false) {
            echo "   🎯 FOUND 'boot' keyword!\n";
        }
        if (stripos($jsonStr, 'time') !== false) {
            echo "   🎯 FOUND 'time' keyword!\n";
        }
        if (stripos($jsonStr, 'runtime') !== false) {
            echo "   🎯 FOUND 'runtime' keyword!\n";
        }
    } else {
        echo "   ❌ HTTP $httpCode\n";
        if ($httpCode !== 404) {
            echo "   Error: " . substr($response, 0, 100) . "...\n";
        }
    }

    echo "\n";
}

echo "✅ Check complete!\n";
echo "💡 If you see 'FOUND' messages above, that endpoint has boot time info.\n";
?>
