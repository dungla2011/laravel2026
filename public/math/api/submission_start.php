<?php
// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Log start
file_put_contents(__DIR__ . '/../error.log', date('Y-m-d H:i:s') . " - submission_start.php called\n", FILE_APPEND);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    file_put_contents(__DIR__ . '/../error.log', "Loading database...\n", FILE_APPEND);
    require_once __DIR__ . '/../database.php';
    
    file_put_contents(__DIR__ . '/../error.log', "Loading helpers...\n", FILE_APPEND);
    require_once __DIR__ . '/helpers.php';
    
    file_put_contents(__DIR__ . '/../error.log', "Verifying token...\n", FILE_APPEND);
    $userId = verifyToken();
    
    file_put_contents(__DIR__ . '/../error.log', "User ID: $userId\n", FILE_APPEND);
    $db = Database::getInstance();
    
    $body = json_decode(file_get_contents('php://input'), true);
    $exerciseId = $body['exerciseId'] ?? null;
    
    file_put_contents(__DIR__ . '/../error.log', "Exercise ID: $exerciseId\n", FILE_APPEND);
    
    if (!$exerciseId) {
        http_response_code(400);
        echo json_encode(['error' => 'Exercise ID required']);
        exit;
    }
    
    file_put_contents(__DIR__ . '/../error.log', "Inserting to database...\n", FILE_APPEND);
    $db->query(
        "INSERT INTO submissions (user_id, exercise_id, start_time)
         VALUES (?, ?, datetime('now'))",
        [$userId, $exerciseId]
    );
    
    $submissionId = $db->lastInsertId();
    file_put_contents(__DIR__ . '/../error.log', "Submission ID: $submissionId\n", FILE_APPEND);
    
    echo json_encode(['submissionId' => $submissionId], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $error = "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    file_put_contents(__DIR__ . '/../error.log', $error, FILE_APPEND);
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
