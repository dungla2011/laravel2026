<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/helpers.php';

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

$userId = verifyToken();
$db = Database::getInstance();

$history = $db->fetchAll(
    "SELECT s.id, s.exercise_id, e.name, e.type, s.start_time, s.end_time, 
            s.score, s.total_questions,
            CAST((julianday(s.end_time) - julianday(s.start_time)) * 86400 AS INTEGER) as duration_seconds,
            (SELECT COUNT(*) FROM answers WHERE submission_id = s.id AND user_answer IS NOT NULL) as answered_count
     FROM submissions s
     JOIN exercises e ON s.exercise_id = e.id
     WHERE s.user_id = ?
     ORDER BY s.start_time DESC",
    [$userId]
);

echo json_encode($history, JSON_UNESCAPED_UNICODE);
