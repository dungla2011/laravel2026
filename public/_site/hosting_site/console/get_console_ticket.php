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

if(!isAdminCookie()){
    die("Not admin site");
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get parameters
$vmName = $_REQUEST['vm'] ?? '';
$esxiHost = $_REQUEST['host'] ?? '';

if (empty($vmName) || empty($esxiHost)) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required parameters: vm and host'
    ]);
    exit;
}

// Path to Python script
$scriptPath = __DIR__ . '/get_console_ticket.py';

//var/www/html/public/_site/hosting_site/console/venv/bin/python

$path = __DIR__.'/venv/bin/python';

// die("PATH = $path");

// Build command
$command = sprintf(
    $path.' %s vm=%s host=%s 2>&1',
    escapeshellarg($scriptPath),
    escapeshellarg($vmName),
    escapeshellarg($esxiHost)
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
