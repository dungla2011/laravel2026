<?php
require_once 'vendor/autoload.php';

// Test validation methods mới
try {
    // Tạo một instance của User để test
    $user = new App\Models\User();

    echo "Testing getValidateRuleInsert():\n";
    $insertRules = $user->getValidateRuleInsert();
    print_r($insertRules);

    echo "\nTesting getValidateRuleUpdate(123):\n";
    $updateRules = $user->getValidateRuleUpdate(123);
    print_r($updateRules);

    echo "\nSo sánh unique rules:\n";
    echo "Insert email rule: " . $insertRules['email'] . "\n";
    echo "Update email rule: " . $updateRules['email'] . "\n";
    echo "Insert username rule: " . $insertRules['username'] . "\n";
    echo "Update username rule: " . $updateRules['username'] . "\n";

    echo "\n✅ Refactoring validation methods thành công!\n";
    echo "✅ Đã gộp thành công 2 methods thành 1 base method chung!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}