<?php
require_once __DIR__ . '/../database.php';

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

$db = Database::getInstance();
$body = json_decode(file_get_contents('php://input'), true);

$username = $body['username'] ?? '';
$password = $body['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

try {
    $db->query(
        'INSERT INTO users (username, password) VALUES (?, ?)',
        [$username, $hashedPassword]
    );
    $userId = $db->lastInsertId();
    echo json_encode(['message' => 'User registered successfully', 'userId' => $userId], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Username already exists']);
}
