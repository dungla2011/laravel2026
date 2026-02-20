<?php
// Composer autoload
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/database.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Define BASE_PATH for subfolder deployment
// Change this to match your deployment folder (e.g., '/math/', '/app/', or '/' for root)
define('BASE_PATH', '/math/');

// Constants
define('SECRET_KEY', 'your-secret-key-change-this');
define('JWT_ALGORITHM', 'HS256');

// Database instance
$db = Database::getInstance();

// Helper function to generate URLs with BASE_PATH
function url($path = '') {
    $path = ltrim($path, '/');
    return rtrim(BASE_PATH, '/') . ($path ? '/' . $path : '');
}

// Helper Functions
function sendJSON($data, $code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sendError($message, $code = 400) {
    sendJSON(['error' => $message], $code);
}

function getRequestBody() {
    return json_decode(file_get_contents('php://input'), true);
}

function setApiHeaders() {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function verifyToken() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (empty($authHeader)) {
        sendError('No token provided', 401);
    }
    
    $parts = explode(' ', $authHeader);
    if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
        sendError('Invalid token format', 401);
    }
    
    try {
        $decoded = JWT::decode($parts[1], new Key(SECRET_KEY, JWT_ALGORITHM));
        return $decoded->id;
    } catch (Exception $e) {
        sendError('Invalid token: ' . $e->getMessage(), 401);
    }
}

// Router
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string and parse path
$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);

// Remove BASE_PATH from the beginning
if (BASE_PATH !== '/' && strpos($path, BASE_PATH) === 0) {
    $path = substr($path, strlen(BASE_PATH) - 1);
}

// Handle page routing via ?act parameter
if ($requestMethod === 'GET' && ($path === '/math/' || $path === '/math' || $path === '/' || $path === '')) {
    if (!isset($_GET['act'])) {
        $_GET['act'] = 'login';
    }
    
    $act = $_GET['act'];

    switch ($act) {
        case 'login':
            include __DIR__ . '/pages/login.php';
            exit;
        case 'dashboard':
            include __DIR__ . '/pages/dashboard.php';
            exit;
        case 'quiz':
            include __DIR__ . '/pages/quiz.php';
            exit;
        case 'result':
            include __DIR__ . '/pages/result.php';
            exit;
        case 'admin':
            include __DIR__ . '/pages/admin.php';
            exit;
        case 'user_history_admin':
            include __DIR__ . '/pages/user_history_admin.php';
            exit;
        default:
            sendError('Page not found', 404);
    }
}

// API endpoints are now separate files in /api/ folder
// /api/login.php, /api/register.php, etc.

// SERVE PHP FILES (including API files)
$phpFile = __DIR__ . $path;

// Try .php extension if no extension
if (!pathinfo($path, PATHINFO_EXTENSION)) {
    $phpFile .= '.php';
}

if (file_exists($phpFile) && is_file($phpFile) && pathinfo($phpFile, PATHINFO_EXTENSION) === 'php') {
    include $phpFile;
    exit;
}

// SERVE STATIC FILES
$filePath = __DIR__ . '/public' . $path;

if (file_exists($filePath) && is_file($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'html' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml'
    ];
    
    $contentType = $mimeTypes[$ext] ?? 'text/plain';
    header('Content-Type: ' . $contentType);
    readfile($filePath);
    exit;
}

sendError('Not found', 404);
