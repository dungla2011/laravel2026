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

$exerciseId = $_GET['id'] ?? null;

if (!$exerciseId) {
    http_response_code(400);
    echo json_encode(['error' => 'Exercise ID required']);
    exit;
}

$attempts = $db->fetchAll(
    "SELECT s.id, s.start_time, s.end_time, s.score, s.total_questions,
            CAST((julianday(s.end_time) - julianday(s.start_time)) * 86400 AS INTEGER) as duration_seconds
     FROM submissions s
     WHERE s.user_id = ? AND s.exercise_id = ?
     ORDER BY s.start_time DESC",
    [$userId, $exerciseId]
);

$totalAttempts = count($attempts);
$avgScore = $totalAttempts > 0 
    ? round(array_sum(array_column($attempts, 'score')) / $totalAttempts) 
    : 0;
$bestScore = $totalAttempts > 0 
    ? max(array_column($attempts, 'score')) 
    : 0;

echo json_encode([
    'totalAttempts' => $totalAttempts,
    'averageScore' => $avgScore,
    'bestScore' => $bestScore,
    'attempts' => $attempts
], JSON_UNESCAPED_UNICODE);
