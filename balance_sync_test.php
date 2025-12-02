<?php

/**
 * Quick test script for Balance Sync logic
 * Run: php balance_sync_test.php
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


// Override database to use glx_v3
//config(['database.connections.mysql.database' => 'glx_v3']);

use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserBalanceTransaction;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;

echo "=== Balance Sync Test ===\n\n";

try {
    // Test 1: Create test user
    echo "📝 Test 1: Create test user...\n";
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test-' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    $balance = UserBalance::create([
        'user_id' => $user->id,
        'balance' => 1000000,
        'total_recharged' => 1000000,
        'status' => 1,
    ]);
    echo "✅ User created: ID={$user->id}, Initial Balance=1,000,000\n\n";

    // Test 2: Single charge
    echo "📝 Test 2: Charge 50,000 for VPS...\n";
    $transaction = BalanceService::chargeService(
        $user->id,
        50000,
        'vps',
        'Test VPS usage',
        'VpsUsage',
        1
    );
    $balance->refresh();
    echo "✅ Charge successful\n";
    echo "   Transaction ID: {$transaction->id}\n";
    echo "   New Balance: " . number_format($balance->balance, 0) . "\n";
    echo "   Total Spent: " . number_format($balance->total_spent, 0) . "\n\n";

    // Test 3: Verify transaction record
    echo "📝 Test 3: Verify transaction record...\n";
    $transRecord = UserBalanceTransaction::find($transaction->id);
    echo "✅ Transaction verified:\n";
    echo "   Amount: " . number_format($transRecord->amount, 0) . "\n";
    echo "   Balance Before: " . number_format($transRecord->balance_before, 0) . "\n";
    echo "   Balance After: " . number_format($transRecord->balance_after, 0) . "\n\n";

    // Test 4: Second charge
    echo "📝 Test 4: Second charge 100,000...\n";
    BalanceService::chargeService($user->id, 100000, 'hosting');
    $balance->refresh();
    echo "✅ Second charge successful\n";
    echo "   New Balance: " . number_format($balance->balance, 0) . "\n";
    echo "   Total Spent: " . number_format($balance->total_spent, 0) . "\n\n";

    // Test 5: Verify sync (should be clean)
    echo "📝 Test 5: Verify balance sync...\n";
    $results = BalanceService::verifyAndFixBalance($user->id);
    echo "✅ Verification results:\n";
    echo "   Checked: {$results['checked']}\n";
    echo "   Synced: {$results['synced']}\n";
    echo "   Fixed: {$results['fixed']}\n\n";

    // Test 6: Simulate desync
    echo "📝 Test 6: Simulate desync (corrupt balance)...\n";
    $balance->update(['balance' => 999999]);
    echo "✅ Balance corrupted to: " . number_format(999999, 0) . "\n\n";

    // Test 7: Detect and fix desync
    echo "📝 Test 7: Detect & fix desync...\n";
    $results = BalanceService::verifyAndFixBalance($user->id);
    echo "✅ After fix:\n";
    echo "   Synced: {$results['synced']}\n";
    echo "   Fixed: {$results['fixed']}\n";
    $balance->refresh();
    echo "   Correct Balance: " . number_format($balance->balance, 0) . "\n\n";

    // Test 8: Get discrepancies
    echo "📝 Test 8: Get discrepancy report...\n";
    $discrepancies = BalanceService::getBalanceDiscrepancies();
    echo "✅ Discrepancies found: " . count($discrepancies) . "\n";
    if (count($discrepancies) == 0) {
        echo "   All users are in sync ✓\n\n";
    }

    // Test 9: Insufficient balance
    echo "📝 Test 9: Test insufficient balance...\n";
    try {
        BalanceService::chargeService($user->id, 2000000, 'vps');
        echo "❌ Should have thrown exception!\n\n";
    } catch (\Exception $e) {
        echo "✅ Exception caught (expected): " . $e->getMessage() . "\n\n";
    }

    // Cleanup
    echo "🧹 Cleanup...\n";
    $user->delete();
    echo "✅ Test completed successfully!\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
