<?php

// VBee TTS API Configuration
const VBEE_API_URL = 'https://vbee.vn/api/v1/tts';
const VBEE_BEARER_TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NTM0OTU3MzB9.W_EgGAfLtMDsP-7AZcr-584_8j3fXBQLcUtDh8LSq00';
const VBEE_APP_ID = '151d5058-6f2e-4975-8a04-22d7e06b6c12';

/**
 * Gọi VBee TTS API để tạo âm thanh
 */
function vbeeTtsRequest($inputText, $voiceCode = 'hn_female_ngochuyen_full_48k-fhg', $options = []) {
    $payload = array_merge([
        "app_id" => VBEE_APP_ID,
        "response_type" => "indirect",
        "callback_url" => "https://mydomain/callback",
        "input_text" => $inputText,
        "voice_code" => $voiceCode,
        "audio_type" => "mp3",
        "bitrate" => 128,
        "speed_rate" => "1.0"
    ], $options);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, VBEE_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . VBEE_BEARER_TOKEN,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'data' => json_decode($response, true),
        'error' => $curlError
    ];
}

/**
 * Lấy kết quả âm thanh từ VBee TTS API
 */
function vbeeTtsGetResult($requestId) {
    $callbackUrl = VBEE_API_URL . "/$requestId/callback-result";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $callbackUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . VBEE_BEARER_TOKEN,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'data' => json_decode($response, true),
        'error' => $curlError,
        'callback_url' => $callbackUrl
    ];
}

// Handle actions
$action = $action ?? 'request';

if ($action === 'request') {
    // Request action - send text to VBee TTS API
    $text = $text ?? '';
    $voice = $voice ?? 'hn_female_ngochuyen_full_48k-fhg';
    
    if (empty($text)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập văn bản'
        ]);
        exit;
    }
    
    $result = vbeeTtsRequest($text, $voice);
    
    if ($result['http_code'] === 200 && isset($result['data']['result']['request_id'])) {
        echo json_encode([
            'success' => true,
            'request_id' => $result['data']['result']['request_id'],
            'status' => $result['data']['result']['status'] ?? 'SUBMITTED',
            'message' => 'Yêu cầu được gửi thành công'
        ]);
    } else {
        $errorMsg = 'Lỗi khi gửi yêu cầu';
        if (isset($result['data']['message'])) {
            $errorMsg = $result['data']['message'];
        } elseif ($result['error']) {
            $errorMsg = $result['error'];
        }
        
        echo json_encode([
            'success' => false,
            'message' => $errorMsg,
            'http_code' => $result['http_code']
        ]);
    }
    exit;
    
} elseif ($action === 'getresult') {
    // GetResult action - check if audio is ready
    $requestId = $requestId ?? '';
    
    if (empty($requestId)) {
        echo json_encode([
            'success' => false,
            'error' => 'Request ID không hợp lệ'
        ]);
        exit;
    }
    
    $result = vbeeTtsGetResult($requestId);
    
    if ($result['http_code'] === 200 && isset($result['data']['result'])) {
        $resultData = $result['data']['result'];
        
        // Check if audio is ready in payload
        $audioUrl = null;
        $status = 'UNKNOWN';
        
        // Check payload for audio (VBee returns audio_link in payload)
        if (isset($resultData['payload']['audio_link']) && isset($resultData['payload']['status'])) {
            $audioUrl = $resultData['payload']['audio_link'];
            $status = $resultData['payload']['status'];
        }
        // Fallback: check if status is directly in result
        else if (isset($resultData['status'])) {
            $status = $resultData['status'];
            if (isset($resultData['audio_url'])) {
                $audioUrl = $resultData['audio_url'];
            }
        }
        
        if ($status === 'SUCCESS' && $audioUrl) {
            // Audio is ready
            echo json_encode([
                'success' => true,
                'audio_url' => $audioUrl,
                'status' => $status,
                'request_id' => $requestId
            ]);
        } else if ($status === 'PROCESSING' || $status === 'IN_PROGRESS' || $status === 'SUBMITTED') {
            // Still processing
            echo json_encode([
                'success' => false,
                'processing' => true,
                'status' => $status,
                'message' => 'Đang xử lý...'
            ]);
        } else {
            // Other status
            echo json_encode([
                'success' => false,
                'error' => 'Trạng thái không mong muốn: ' . $status
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Không thể lấy kết quả: ' . ($result['error'] ?? 'HTTP ' . $result['http_code'])
        ]);
    }
    exit;
    
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Action không hợp lệ'
    ]);
    exit;
}
