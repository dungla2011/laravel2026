<?php
require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../helpers.php';

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

$adminUserId = verifyToken();
$db = Database::getInstance();

$data = json_decode(file_get_contents('php://input'), true);
$submissionIds = $data['submission_ids'] ?? [];

if (empty($submissionIds)) {
    http_response_code(400);
    echo json_encode(['error' => 'submission_ids is required']);
    exit;
}

try {
    // Delete answers first (foreign key constraint)
    $placeholders = implode(',', array_fill(0, count($submissionIds), '?'));
    
    $db->query(
        "DELETE FROM answers WHERE submission_id IN ($placeholders)",
        $submissionIds
    );
    
    // Delete submissions
    $db->query(
        "DELETE FROM submissions WHERE id IN ($placeholders)",
        $submissionIds
    );
    
    echo json_encode([
        'success' => true,
        'deleted_count' => count($submissionIds)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
