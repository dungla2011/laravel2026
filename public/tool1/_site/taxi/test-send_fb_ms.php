<?php

date_default_timezone_set("Asia/Ho_Chi_Minh");
// ========== TAXI DRIVER APP - Test Different Sound Priorities ==========
// Script to test custom notification sounds with different priorities
// ✅ FIXED: Now only 1 sound per notification (no duplicate)
require_once 'lib_taxi.php';


try {

    echo "🔍 Starting Firebase Messaging Test...\n\n";
    
    // Debug service account
    echo "📋 Service Account Info:\n";
    debugServiceAccount($SERVICE_ACCOUNT_FILE);
    echo "\n";
    
    // Get access token
    echo "🔑 Getting access token...\n";
    $accessToken = getAccessToken($SERVICE_ACCOUNT_FILE);
    echo "✅ Access token received: " . substr($accessToken, 0, 20) . "...\n\n";
        
    // Test notification
    echo "📱 Sending test notification...\n";
    $urgentData = [
        "booking_id" => "SOUND_TEST_URGENT_" . time(),
        "pickup_location" => "Test Location - Debug JWT",
        "destination" => "Test Destination",
        "priority" => "urgent"
    ];
    
    $response = sendNotificationV1(
        $PROJECT_ID,
        $accessToken,
        $FCM_TOKEN,
        "🚨 JWT Fixed Test",
        "Testing after JWT signature fix",
        $urgentData
    );
    
    echo $response['success'] ? "✅ Notification Sent Successfully!" : "❌ Notification Failed";
    echo "\n";
    
    if (!$response['success'] && isset($response['error'])) {
        echo "Error details: " . $response['error'] . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n==========================================\n";
echo "💡 Instructions:\n";
echo "1. Test with app in FOREGROUND - listen for AudioPlayer sound\n";
echo "2. Test with app in BACKGROUND - listen for system sound\n";
echo "3. Should only hear 1 sound per test (no duplicate)\n";
echo "4. Different priorities = different sounds\n\n";
?>