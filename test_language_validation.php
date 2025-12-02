<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$user = new App\Models\User();
$rules = $user->getValidateRuleUpdate(11735492256858114);

echo "=== NEW VALIDATION RULES ===\n";
foreach ($rules as $field => $rule) {
    echo "$field: $rule\n";
}

echo "\n=== TEST WITH INVALID DATA ===\n";
$testData = ['language' => 'vi1'];

$validator = \Illuminate\Support\Facades\Validator::make($testData, $rules);

if ($validator->fails()) {
    echo "❌ VALIDATION FAILED (Good!):\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "✅ VALIDATION PASSED (Bad - should fail!)\n";
}

echo "\n=== TEST WITH VALID DATA ===\n";
$testData = ['language' => 'vi'];

$validator = \Illuminate\Support\Facades\Validator::make($testData, $rules);

if ($validator->fails()) {
    echo "❌ VALIDATION FAILED (Bad - should pass!):\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "✅ VALIDATION PASSED (Good!)\n";
}