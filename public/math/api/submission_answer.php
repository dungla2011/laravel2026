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
$questionId = $body['question_id'] ?? null;
$userAnswer = $body['user_answer'] ?? null;

if (!$submissionId || !$questionId || $userAnswer === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Submission ID, question ID and answer required']);
    exit;
}

// Check if answer already exists
$existing = $db->fetchOne(
    'SELECT id FROM answers WHERE submission_id = ? AND question_id = ?',
    [$submissionId, $questionId]
);

// Get correct answer
$question = $db->fetchOne(
    'SELECT answer FROM questions WHERE id = ?',
    [$questionId]
);

if (!$question) {
    http_response_code(404);
    echo json_encode(['error' => 'Question not found']);
    exit;
}

$isCorrect = ($question['answer'] == $userAnswer);

if ($existing) {
    // Update existing answer
    $db->query(
        'UPDATE answers SET user_answer = ?, is_correct = ? WHERE id = ?',
        [$userAnswer, $isCorrect ? 1 : 0, $existing['id']]
    );
} else {
    // Insert new answer
    $db->query(
        'INSERT INTO answers (submission_id, question_id, user_answer, is_correct)
         VALUES (?, ?, ?, ?)',
        [$submissionId, $questionId, $userAnswer, $isCorrect ? 1 : 0]
    );
}

// Calculate current score
$stats = $db->fetchOne(
    'SELECT COUNT(*) as total, SUM(is_correct) as correct FROM answers WHERE submission_id = ?',
    [$submissionId]
);

$score = $stats['total'] > 0 ? round(($stats['correct'] / $stats['total']) * 100) : 0;

echo json_encode([
    'is_correct' => $isCorrect,
    'correct_answer' => $question['answer'],
    'score' => $score
], JSON_UNESCAPED_UNICODE);
