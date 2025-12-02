<?php
// filepath: debug_jwt_only.php
require_once 'lib_taxi.php';

function testJWT() {
    $serviceAccountFile = "/var/www/html/config/service-account-key-firebase-taxi.json";
    
    $serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);
    $privateKey = str_replace('\\n', "\n", $serviceAccount['private_key']);
    
    $now = time();
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $payload = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600
    ];
    
    $headerEncoded = base64UrlEncode(json_encode($header));
    $payloadEncoded = base64UrlEncode(json_encode($payload));
    $data = $headerEncoded . '.' . $payloadEncoded;
    
    echo "Header: " . json_encode($header) . "\n";
    echo "Payload: " . json_encode($payload) . "\n";
    echo "Data to sign: $data\n\n";
    
    // Test với Firebase Admin SDK scope khác
    $payload2 = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600
    ];
    
    $payloadEncoded2 = base64UrlEncode(json_encode($payload2));
    $data2 = $headerEncoded . '.' . $payloadEncoded2;
    
    echo "Alternative payload (cloud-platform scope): " . json_encode($payload2) . "\n";
    echo "Alternative data: $data2\n";
}

testJWT();