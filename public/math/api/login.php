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

$db = Database::getInstance();
$body = json_decode(file_get_contents('php://input'), true);

$username = $body['username'] ?? '';
$password = $body['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

$user = $db->fetchOne(
    'SELECT id, password FROM users WHERE username = ?',
    [$username]
);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$token = generateToken($user['id']);
echo json_encode([
    'token' => $token,
    'userId' => $user['id'],
    'username' => $username
], JSON_UNESCAPED_UNICODE);
