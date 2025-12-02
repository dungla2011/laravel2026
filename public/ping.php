<?php

/**
 * Simple ping endpoint for network connectivity check
 * Returns 200 OK with minimal response
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Return simple pong response
echo json_encode([
    'status' => 'ok',
    'message' => 'pong',
    'timestamp' => time(),
    'server_time' => date('Y-m-d H:i:s'),
]);
