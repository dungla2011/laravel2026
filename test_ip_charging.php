<?php
// Test script for IP charging logic

require_once 'vendor/autoload.php';

use App\Services\VpsUsageFeeService;

// Test cases
$testCases = [
    [
        'name' => 'Only local IPs (should be 0 chargeable)',
        'ips' => '192.168.1.1, 10.0.0.1, 172.16.0.1',
        'expected' => 0,
    ],
    [
        'name' => '1 Internet IP (should be 0 chargeable - 1 free)',
        'ips' => '8.8.8.8',
        'expected' => 0,
    ],
    [
        'name' => '2 Internet IPs (should be 1 chargeable)',
        'ips' => '8.8.8.8, 1.1.1.1',
        'expected' => 1,
    ],
    [
        'name' => '3 Internet IPs (should be 2 chargeable)',
        'ips' => '8.8.8.8, 1.1.1.1, 208.67.222.222',
        'expected' => 2,
    ],
    [
        'name' => 'Mixed IPs: 2 local + 2 internet (should be 1 chargeable)',
        'ips' => '192.168.1.1, 8.8.8.8, 1.1.1.1, 10.0.0.1',
        'expected' => 1,
    ],
    [
        'name' => 'Mixed IPs: 3 local + 3 internet (should be 2 chargeable)',
        'ips' => '192.168.1.1, 8.8.8.8, 1.1.1.1, 10.0.0.1, 172.16.0.1, 208.67.222.222',
        'expected' => 2,
    ],
    [
        'name' => 'Empty IPs (should be 0 chargeable)',
        'ips' => '',
        'expected' => 0,
    ],
    [
        'name' => 'Localhost (should be 0 chargeable)',
        'ips' => '127.0.0.1',
        'expected' => 0,
    ],
    [
        'name' => 'Link-local IP (should be 0 chargeable)',
        'ips' => '169.254.1.1',
        'expected' => 0,
    ],
];

echo "=== IP Charging Logic Tests ===\n\n";

$passed = 0;
$failed = 0;

foreach ($testCases as $test) {
    $result = VpsUsageFeeService::countChargeableIPs($test['ips']);
    $status = $result === $test['expected'] ? '✓ PASS' : '✗ FAIL';
    
    if ($result === $test['expected']) {
        $passed++;
    } else {
        $failed++;
    }
    
    echo "{$status} | {$test['name']}\n";
    echo "  IPs: {$test['ips']}\n";
    echo "  Expected: {$test['expected']}, Got: {$result}\n\n";
}

echo "=== Summary ===\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";
echo "Total: " . ($passed + $failed) . "\n";

?>
