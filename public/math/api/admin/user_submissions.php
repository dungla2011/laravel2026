<?php
require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$adminUserId = verifyToken();
$db = Database::getInstance();

$targetUserId = $_GET['user_id'] ?? null;

if (!$targetUserId) {
    http_response_code(400);
    echo json_encode(['error' => 'user_id is required']);
    exit;
}

// Get submissions for specific user
$submissions = $db->fetchAll(
    'SELECT 
        s.id,
        s.start_time,
        s.end_time,
        s.score,
        s.total_questions,
        e.name as exercise_name,
        e.type as exercise_type,
        (SELECT COUNT(*) FROM answers WHERE submission_id = s.id AND user_answer IS NOT NULL) as answered_count
    FROM submissions s
    LEFT JOIN exercises e ON s.exercise_id = e.id
    WHERE s.user_id = ?
    ORDER BY s.start_time DESC',
    [$targetUserId]
);

echo json_encode($submissions, JSON_UNESCAPED_UNICODE);
