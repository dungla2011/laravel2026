<?php
/**
 * PHP wrapper to get VMware console ticket
 * Returns JSON with ticket information
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


//
//
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uid = getCurrentUserId();
if(!$uid){
    echo json_encode([
        'success' => false,
        'error' => 'You need login!'
    ]);
    exit;
}

// Get only instance_id parameter
$instanceId = $_REQUEST['instance_id'] ?? '';
$cmd = $_REQUEST['cmd'] ?? '';  // poweron, poweroff, reset

if (empty($instanceId)) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required parameter: instance_id'
    ]);
    exit;
}

$belongVendor = \App\Models\VpsAndUser::where('user_id_vendor', $uid)->where("instance_id", $instanceId)->first();

// Validate instance and get VM details
$vmi = \App\Models\VpsInstance::find($instanceId);
if(!$vmi || ($vmi->user_id != $uid && !$belongVendor)){

    if(!isAdminLrv_()){
        echo json_encode([
            'success' => false,
            'error' => 'Not valid access to this resource-2!'
        ]);
        exit;
    }
}

// Get VM value (bios_uuid) from instance
$vmValue = $vmi->bios_uuid;

// Get ESXi host from vps_usage
$vmUsage = \App\Models\VpsUsage::where("bios_uuid", $vmValue)->where('name',$vmi->name)->orderBy('id', 'DESC')->first();
if(!$vmUsage){
    echo json_encode([
        'success' => false,
        'error' => 'VPS usage record not found!'
    ]);
    exit;
}

$esxiHost = $vmUsage->last_host_ip;
if (empty($esxiHost)) {
    echo json_encode([
        'success' => false,
        'error' => 'Not found host of vm!'
    ]);
    exit;
}

// Path to Python script
$scriptPath = __DIR__ . '/get_console_ticket.py';

//var/www/html/public/_site/hosting_site/console/venv/bin/python

$path = __DIR__.'/venv/bin/python';

// die("PATH = $path");

// Build command - use bios_uuid as vm identifier
$command = sprintf(
    $path.' %s vm=%s host=%s vm_type=id cmd=%s 2>&1',
    escapeshellarg($scriptPath),
    escapeshellarg($vmValue),
    escapeshellarg($esxiHost),
    escapeshellarg($cmd)
);

// Execute Python script
$output = shell_exec($command);

// Try to parse JSON output
$result = json_decode($output, true);

if ($result === null) {
    // Failed to parse JSON
    echo json_encode([
        'success' => false,
        'error' => 'Failed to get console ticket',
        'debug_output' => $output
    ]);
} else {
    echo json_encode($result);
}
