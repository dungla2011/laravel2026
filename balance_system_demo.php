#!/usr/bin/env php
<?php

/**
 * Balance System Test/Demo Script
 * 
 * Usage: php balance_system_demo.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserRecharge;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;

echo "\n=== User Balance System Demo ===\n\n";

// Get first user (or create test user)
$user = User::first() ?? User::create([
    'name' => 'Test User',
    'email' => 'test-' . time() . '@example.com',
    'password' => bcrypt('password'),
]);

echo "Using User ID: {$user->id} ({$user->name})\n";

// Ensure user has balance record
$balance = UserBalance::firstOrCreate(
    ['user_id' => $user->id],
    [
        'balance' => 0,
        'status' => 1,
        'low_balance_threshold' => 10000,
    ]
);

echo "\n--- Initial Balance ---\n";
$info = BalanceService::getBalanceInfo($user->id);
echo "Balance: ₫" . number_format($info['balance'], 0) . "\n";
echo "Total Recharged: ₫" . number_format($info['total_recharged'], 0) . "\n";
echo "Total Spent: ₫" . number_format($info['total_spent'], 0) . "\n";

// Demo 1: Create Recharge
echo "\n--- Demo 1: Create Recharge ---\n";
try {
    $recharge = BalanceService::createRecharge(
        userId: $user->id,
        amount: 500000,
        paymentMethod: 'bank_transfer'
    );
    echo "✅ Created recharge: #{$recharge->id}\n";
    echo "   Amount: ₫" . number_format($recharge->amount, 0) . "\n";
    echo "   Status: {$recharge->status}\n";
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

// Demo 2: Complete Recharge (Simulate payment gateway callback)
echo "\n--- Demo 2: Complete Recharge ---\n";
try {
    BalanceService::completeRecharge(
        rechargeId: $recharge->id,
        gatewayResponse: [
            'transaction_id' => 'TXN_' . time(),
            'status' => 'success',
        ]
    );
    echo "✅ Recharge completed\n";
    
    $info = BalanceService::getBalanceInfo($user->id);
    echo "   New Balance: ₫" . number_format($info['balance'], 0) . "\n";
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

// Demo 3: Charge Service (VPS Usage)
echo "\n--- Demo 3: Charge Service (VPS) ---\n";
try {
    BalanceService::chargeService(
        userId: $user->id,
        amount: 1500,
        serviceType: 'vps',
        description: 'VPS Instance #1 per-minute charge',
        referenceModel: 'VpsInstance',
        referenceId: 1
    );
    echo "✅ Service charged\n";
    
    $info = BalanceService::getBalanceInfo($user->id);
    echo "   New Balance: ₫" . number_format($info['balance'], 0) . "\n";
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

// Demo 4: Charge Multiple Times (Simulate multiple minutes)
echo "\n--- Demo 4: Charge Multiple Times ---\n";
for ($i = 0; $i < 3; $i++) {
    try {
        BalanceService::chargeService(
            userId: $user->id,
            amount: 1500,
            serviceType: 'vps',
            description: "VPS usage minute " . ($i + 2),
            referenceModel: 'VpsInstance',
            referenceId: 1
        );
        echo "✅ Charge #" . ($i + 2) . " successful\n";
    } catch (\Exception $e) {
        echo "❌ Charge #" . ($i + 2) . " failed: {$e->getMessage()}\n";
        break;
    }
}

$info = BalanceService::getBalanceInfo($user->id);
echo "   Balance after charges: ₫" . number_format($info['balance'], 0) . "\n";

// Demo 5: Transaction History
echo "\n--- Demo 5: Transaction History ---\n";
$transactions = BalanceService::getTransactionHistory($user->id, 10);
echo "Total transactions: " . count($transactions) . "\n";
foreach ($transactions as $txn) {
    $sign = $txn->amount >= 0 ? '+' : '';
    echo "  [{$txn->transaction_type}] {$sign}₫" . number_format(abs($txn->amount), 0) . " → ₫" . number_format($txn->balance_after, 0) . " | {$txn->description}\n";
}

// Demo 6: Recharge History
echo "\n--- Demo 6: Recharge History ---\n";
$recharges = BalanceService::getRechargeHistory($user->id, 5);
echo "Total recharges: " . count($recharges) . "\n";
foreach ($recharges as $rch) {
    echo "  [#{$rch->id}] ₫" . number_format($rch->amount, 0) . " - {$rch->payment_method} ({$rch->status})\n";
}

// Demo 7: Test Insufficient Balance
echo "\n--- Demo 7: Test Insufficient Balance ---\n";
$currentBalance = UserBalance::where('user_id', $user->id)->first()->balance;
$chargeAmount = $currentBalance + 100000;  // More than available

try {
    BalanceService::chargeService(
        userId: $user->id,
        amount: $chargeAmount,
        serviceType: 'vps',
        description: 'Test charge (should fail)',
        referenceModel: 'VpsInstance',
        referenceId: 999
    );
} catch (\Exception $e) {
    echo "✅ Correctly rejected: {$e->getMessage()}\n";
    echo "   Available: ₫" . number_format($currentBalance, 0) . "\n";
    echo "   Requested: ₫" . number_format($chargeAmount, 0) . "\n";
}

// Final Summary
echo "\n--- Final Summary ---\n";
$info = BalanceService::getBalanceInfo($user->id);
echo "User: {$user->name} (ID: {$user->id})\n";
echo "Balance: ₫" . number_format($info['balance'], 0) . "\n";
echo "Total Recharged: ₫" . number_format($info['total_recharged'], 0) . "\n";
echo "Total Spent: ₫" . number_format($info['total_spent'], 0) . "\n";
echo "Account Status: " . ($info['is_frozen'] ? 'FROZEN' : 'ACTIVE') . "\n";
echo "Service Status: " . ($info['is_suspended'] ? 'SUSPENDED' : 'RUNNING') . "\n";

echo "\n=== Demo Complete ===\n\n";
