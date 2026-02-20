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

$exercise = $db->fetchOne(
    'SELECT * FROM exercises WHERE id = ?',
    [$exerciseId]
);

if (!$exercise) {
    http_response_code(404);
    echo json_encode(['error' => 'Exercise not found']);
    exit;
}

$questions = $db->fetchAll(
    'SELECT q.id, q.num1, q.num2, q.operation, eq.question_order
     FROM questions q
     JOIN exercise_questions eq ON q.id = eq.question_id
     WHERE eq.exercise_id = ?
     ORDER BY eq.question_order',
    [$exerciseId]
);

$formattedQuestions = array_map(function($q) {
    $num1 = $q['num1'];
    $num2 = $q['num2'];
    
    // For subtraction, always put bigger number first (num1 >= num2)
    if ($q['operation'] === '-' && $num1 < $num2) {
        $temp = $num1;
        $num1 = $num2;
        $num2 = $temp;
    }
    
    return [
        'id' => $q['id'],
        'num1' => $num1,
        'num2' => $num2,
        'operation' => $q['operation'],
        'order' => $q['question_order']
    ];
}, $questions);

echo json_encode([
    'exercise' => $exercise,
    'questions' => $formattedQuestions
], JSON_UNESCAPED_UNICODE);
