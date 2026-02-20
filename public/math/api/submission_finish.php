<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/helpers.php';

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

$userId = verifyToken();
$db = Database::getInstance();

$body = json_decode(file_get_contents('php://input'), true);
$submissionId = $body['submission_id'] ?? null;

if (!$submissionId) {
    http_response_code(400);
    echo json_encode(['error' => 'Submission ID required']);
    exit;
}

// Get submission info to find exercise
$submission = $db->fetchOne(
    'SELECT exercise_id FROM submissions WHERE id = ?',
    [$submissionId]
);

if (!$submission) {
    http_response_code(404);
    echo json_encode(['error' => 'Submission not found']);
    exit;
}

// Get total questions count from exercise
$exercise = $db->fetchOne(
    'SELECT question_count FROM exercises WHERE id = ?',
    [$submission['exercise_id']]
);

$totalQuestions = $exercise['question_count'] ?? 0;

// Get answers stats
$stats = $db->fetchOne(
    'SELECT COUNT(*) as answered, SUM(is_correct) as correct FROM answers WHERE submission_id = ?',
    [$submissionId]
);

$correctCount = $stats['correct'] ?? 0;
$score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

// Update submission
$db->query(
    "UPDATE submissions SET end_time = datetime('now'), score = ?, total_questions = ? WHERE id = ?",
    [$score, $totalQuestions, $submissionId]
);

echo json_encode([
    'message' => 'Submission finished',
    'score' => $score,
    'total' => $totalQuestions,
    'correct' => $correctCount
], JSON_UNESCAPED_UNICODE);
