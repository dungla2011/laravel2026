<?php
// Simple token-based auth without JWT library
// For production, use proper JWT library with composer install

define('SECRET_KEY', 'your-secret-key-change-this-in-production');

function verifyToken() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (empty($authHeader)) {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
        exit;
    }
    
    $parts = explode(' ', $authHeader);
    if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token format']);
        exit;
    }
    
    $token = $parts[1];
    
    try {
        // Simple token decoding (base64 + signature verification)
        // Token format: base64(userId.expiry).signature
        if (strpos($token, '.') === false) {
            throw new Exception('Invalid token structure');
        }
        
        list($payload, $signature) = explode('.', $token, 2);
        
        // Verify signature
        $expectedSignature = hash_hmac('sha256', $payload, SECRET_KEY);
        if (!hash_equals($expectedSignature, $signature)) {
            throw new Exception('Invalid signature');
        }
        
        // Decode payload
        $decoded = base64_decode($payload);
        if ($decoded === false || strpos($decoded, ':') === false) {
            throw new Exception('Invalid payload format');
        }
        
        list($userId, $expiry) = explode(':', $decoded, 2);
        
        // Check expiry
        if (time() > (int)$expiry) {
            throw new Exception('Token expired');
        }
        
        return (int)$userId;
        
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token: ' . $e->getMessage()]);
        exit;
    }
}

function generateToken($userId) {
    $expiry = time() + (300 * 24 * 60 * 60); // 30 days (1 month)
    $payload = base64_encode($userId . ':' . $expiry);
    $signature = hash_hmac('sha256', $payload, SECRET_KEY);
    return $payload . '.' . $signature;
}

