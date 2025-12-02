<?php

require_once '/var/www/html/public/index.php';

$client = new Google_Client(['client_id' => '211733424826-obf5srn39771j4quc7c3pedncvfae161.apps.googleusercontent.com']);
if(!$id_token = ($_POST['id_token'] ?? '')){
    die("id_token is required");
}

try {
    $payload = $client->verifyIdToken($id_token);
    if ($payload) {
        $userid = $payload['sub'];
        echo json_encode(['status' => 'success', 'userid' => $userid]);
        // Token is valid, proceed with authentication
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID token']);
        // Invalid ID token
    }
} catch (Exception $e) {
    // Handle error
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
