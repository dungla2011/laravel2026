<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

// Test validation với giá trị vi1
$user = new App\Models\User();

echo "=== VALIDATION RULES ===\n";
$rules = $user->getValidateRuleUpdate(11735492256858114);
foreach ($rules as $field => $rule) {
    echo "$field: $rule\n";
}

echo "\n=== PROBLEM ANALYSIS ===\n";
echo "Request data has: 'language' => 'vi1'\n";
echo "But validation rules only check: 'meta_language.vi' and 'meta_language.en'\n";
echo "The field 'language' is NOT in validation rules!\n";
echo "That's why 'vi1' passes validation - it's not being validated at all!\n";