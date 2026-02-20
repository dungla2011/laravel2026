<?php
// Test API submission_start.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test submission_start.php</h1>";

// Test 1: Check files exist
echo "<h2>1. Check files</h2>";
echo "database.php exists: " . (file_exists(__DIR__ . '/database.php') ? '✅' : '❌') . "<br>";
echo "helpers.php exists: " . (file_exists(__DIR__ . '/api/helpers.php') ? '✅' : '❌') . "<br>";

// Test 2: Load database
echo "<h2>2. Load database</h2>";
try {
    require_once __DIR__ . '/database.php';
    $db = Database::getInstance();
    echo "✅ Database loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Load helpers
echo "<h2>3. Load helpers</h2>";
try {
    require_once __DIR__ . '/api/helpers.php';
    echo "✅ Helpers loaded successfully<br>";
    echo "generateToken function exists: " . (function_exists('generateToken') ? '✅' : '❌') . "<br>";
    echo "verifyToken function exists: " . (function_exists('verifyToken') ? '✅' : '❌') . "<br>";
} catch (Exception $e) {
    echo "❌ Helpers error: " . $e->getMessage() . "<br>";
    exit;
}

// Test 4: Generate token
echo "<h2>4. Test generateToken</h2>";
try {
    $token = generateToken(1);
    echo "✅ Token generated: " . substr($token, 0, 50) . "...<br>";
    echo "Token length: " . strlen($token) . "<br>";
} catch (Exception $e) {
    echo "❌ Token generation error: " . $e->getMessage() . "<br>";
}

// Test 5: Test database insert
echo "<h2>5. Test database insert</h2>";
try {
    $db->query(
        "INSERT INTO submissions (user_id, exercise_id, start_time)
         VALUES (?, ?, datetime('now'))",
        [1, 1]
    );
    $id = $db->lastInsertId();
    echo "✅ Insert successful! Submission ID: $id<br>";
    
    // Cleanup
    $db->query("DELETE FROM submissions WHERE id = ?", [$id]);
    echo "✅ Cleanup done<br>";
} catch (Exception $e) {
    echo "❌ Database insert error: " . $e->getMessage() . "<br>";
}

echo "<h2>6. Test complete API call</h2>";
echo "<p>Token: " . $token . "</p>";
echo "<p>Try this curl command:</p>";
echo "<pre>curl -X POST https://lad.vn/math/api/submission_start.php \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -H 'Authorization: Bearer " . $token . "' \\\n";
echo "  -d '{\"exerciseId\": 1}'</pre>";
