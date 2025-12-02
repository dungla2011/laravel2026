<?php
require_once 'vendor/autoload.php';

// Test validation với giá trị vi1
try {
    $user = new App\Models\User();
    
    echo "=== TEST VALIDATION ===\n";
    
    // Lấy validation rules cho update
    $rules = $user->getValidateRuleUpdate(11735492256858114);
    echo "Validation rules:\n";
    foreach ($rules as $field => $rule) {
        echo "  $field: $rule\n";
    }
    
    echo "\n=== TEST VALIDATION DATA ===\n";
    $testData = [
        'name' => '',
        'username' => 'admin12',
        'password' => '',
        'email' => 'admin12@gmail.com',
        'is_admin' => '',
        'token_user' => '',
        'email_active_at' => '2025-10-02 08:41:29',
        'avatar' => '',
        '_roles' => '',
        'language' => 'vi1'  // <- Giá trị problematic
    ];
    
    echo "Test data:\n";
    foreach ($testData as $field => $value) {
        echo "  $field: '$value'\n";
    }
    
    // Validate data
    $validator = \Illuminate\Support\Facades\Validator::make($testData, $rules);
    
    if ($validator->fails()) {
        echo "\n❌ VALIDATION FAILED:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "- $error\n";
        }
    } else {
        echo "\n✅ VALIDATION PASSED (This is the problem!)\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}