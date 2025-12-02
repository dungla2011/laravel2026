<?php

$domain = "events.dav.edu.vn";

// Test 1: Cache status (check auto-update info)
echo "=== Test 1: Cache status with auto-update info ===\n";
$link = "http://events.dav.edu.vn:50000/cache_status";

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

// Test 2: get_face_vector với form-data
echo "=== Test 2: get_face_vector với form-data ===\n";
$link = "http://events.dav.edu.vn:50000/get_face_vector";

$postData = [
    'image_link' => 'https://events.dav.edu.vn/test_cloud_file?fid=4866',
];

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";


die("==================");


// Test 3: get_face_vector với JSON
echo "=== Test 3: get_face_vector với JSON ===\n";
$link = "http://events.dav.edu.vn:50000/get_face_vector";

$postData = json_encode([
    'image_link' => 'https://events.dav.edu.vn/test_cloud_file?fid=4866',
]);

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

// Test 4: Stop auto-update
echo "=== Test 4: Stop auto-update ===\n";
$link = "http://events.dav.edu.vn:50000/stop_auto_update";

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

// Test 5: Start auto-update
echo "=== Test 5: Start auto-update ===\n";
$link = "http://events.dav.edu.vn:50000/start_auto_update";

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

// Test 6: Manual reload cache
echo "=== Test 6: Manual reload cache ===\n";
$link = "http://events.dav.edu.vn:50000/reload_face_cache";

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

// Test 7: Final cache status
echo "=== Test 7: Final cache status ===\n";
$link = "http://events.dav.edu.vn:50000/cache_status";

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

?>
