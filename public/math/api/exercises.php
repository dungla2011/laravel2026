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

$exercises = $db->fetchAll(
    'SELECT id, name, type, question_count, difficulty 
     FROM exercises 
     ORDER BY type, difficulty'
);

$grouped = [
    'cong' => array_values(array_filter($exercises, fn($e) => $e['type'] === 'cong')),
    'tru' => array_values(array_filter($exercises, fn($e) => $e['type'] === 'tru'))
];

echo json_encode($grouped, JSON_UNESCAPED_UNICODE);
