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

$submissionId = $_GET['id'] ?? null;

if (!$submissionId) {
    http_response_code(400);
    echo json_encode(['error' => 'Submission ID required']);
    exit;
}

$submission = $db->fetchOne(
    'SELECT * FROM submissions WHERE id = ? AND user_id = ?',
    [$submissionId, $userId]
);

if (!$submission) {
    http_response_code(404);
    echo json_encode(['error' => 'Submission not found']);
    exit;
}

// Get all questions for this exercise
$allQuestions = $db->fetchAll(
    'SELECT q.id, q.num1, q.num2, q.operation, q.answer, eq.question_order
     FROM questions q
     JOIN exercise_questions eq ON q.id = eq.question_id
     WHERE eq.exercise_id = ?
     ORDER BY eq.question_order',
    [$submission['exercise_id']]
);

// Get user's answers
$userAnswers = $db->fetchAll(
    'SELECT question_id, user_answer, is_correct
     FROM answers
     WHERE submission_id = ?',
    [$submissionId]
);

// Index answers by question_id
$answersMap = [];
foreach ($userAnswers as $ans) {
    $answersMap[$ans['question_id']] = $ans;
}

// Build complete answer list
$formattedAnswers = [];
foreach ($allQuestions as $q) {
    $userAns = $answersMap[$q['id']] ?? null;
    $formattedAnswers[] = [
        'questionId' => $q['id'],
        'num1' => $q['num1'],
        'num2' => $q['num2'],
        'operation' => $q['operation'],
        'correctAnswer' => $q['answer'],
        'userAnswer' => $userAns ? $userAns['user_answer'] : null,
        'isCorrect' => $userAns ? ($userAns['is_correct'] == 1) : false,
        'isAnswered' => $userAns !== null
    ];
}

echo json_encode([
    'submission' => $submission,
    'answers' => $formattedAnswers
], JSON_UNESCAPED_UNICODE);
