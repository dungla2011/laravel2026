<?php
/**
 * Check vCenter Events API để tìm lần PowerOn gần nhất của VM
 * Để lấy boot time từ event logs
 */

// Load Laravel env
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Config từ env
$domain = env('VCENTER_DOMAIN');
$uid = env('VCENTER_UID');
$pw = env('VCENTER_PW');
$vmId = $argv[1] ?? env('VMWARE_VM_ID', 'vm-123');

if (!$domain || !$uid || !$pw) {
    echo "❌ vCenter credentials required!\n";
    echo "Set env: VCENTER_DOMAIN, VCENTER_UID, VCENTER_PW\n";
    exit(1);
}

echo "Logging into vCenter ({$domain})...\n";

// Login vCenter
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://{$domain}/rest/com/vmware/cis/session");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, "{$uid}:{$pw}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Login failed! HTTP {$httpCode}\n";
    exit(1);
}

$loginData = json_decode($response, true);
$sessionId = $loginData['value'] ?? null;

if (!$sessionId) {
    echo "❌ Failed to get session ID\n";
    exit(1);
}

echo "✓ Logged in. SID: " . substr($sessionId, 0, 32) . "...\n\n";

// 1. Thử Events API với filter PowerOn
echo "📍 Testing: vCenter Events API (Filter PowerOn events)\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://{$domain}/rest/vcenter/vm/{$vmId}/guest/networking");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "vmware-api-session-id: {$sessionId}",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   URL: /rest/vcenter/vm/{$vmId}/guest/networking\n";
echo "   HTTP {$httpCode}\n";
if ($httpCode === 200) {
    echo "   Response:\n";
    $decoded = json_decode($response, true);
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
} else {
    echo "   ❌ Failed\n\n";
}

// 2. Thử lấy tất cả events của VM
echo "📍 Testing: All Events for VM\n";
$ch = curl_init();
// Filter by VM MoRef để tìm events liên quan đến VM này
curl_setopt($ch, CURLOPT_URL, "https://{$domain}/api/events");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "vmware-api-session-id: {$sessionId}",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   URL: /api/events\n";
echo "   HTTP {$httpCode}\n";
if ($httpCode === 200) {
    echo "   Response sample (first 5 events):\n";
    $decoded = json_decode($response, true);
    if (is_array($decoded) && count($decoded) > 0) {
        $sample = array_slice($decoded, 0, 5);
        echo json_encode($sample, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }
} else {
    echo "   Response: $response\n";
}

echo "\n✅ Event check complete!\n";
?>
