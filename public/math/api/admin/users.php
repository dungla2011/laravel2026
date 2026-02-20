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

$userId = verifyToken();
$db = Database::getInstance();

// Get all users with submission stats
$users = $db->fetchAll(
    'SELECT 
        u.id,
        u.username,
        u.created_at,
        COUNT(DISTINCT s.id) as total_submissions,
        AVG(s.score) as avg_score
    FROM users u
    LEFT JOIN submissions s ON u.id = s.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC'
);

echo json_encode($users, JSON_UNESCAPED_UNICODE);
