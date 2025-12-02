<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserBalanceTransaction;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BalanceSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean only balance-related tables (don't use RefreshDatabase to avoid affecting other tests)
        $this->cleanBalanceTables();

        // Create balance tables if needed
        $this->createBalanceTables();

        $this->user = User::createUserAdminDefault();

        $this->balance = UserBalance::create([
            'user_id' => $this->user->id,
            'balance' => 1000000,
            'total_recharged' => 1000000,
            'status' => 1,
        ]);

        // Create initial transaction to track the 1,000,000 balance
        UserBalanceTransaction::create([
            'user_id' => $this->user->id,
            'transaction_type' => 'recharge',
            'amount' => 1000000,
            'balance_before' => 0,
            'balance_after' => 1000000,
            'description' => 'Initial test balance',
            'status' => 'completed',
            'transaction_date' => now(),
        ]);
    }

    private function cleanBalanceTables()
    {
        // Only truncate balance-related tables, not entire database
        try {
            DB::statement('TRUNCATE TABLE user_balance_transactions');
        } catch (\Exception $e) {
            // Table might not exist yet
        }
        
        try {
            DB::statement('TRUNCATE TABLE user_recharges');
        } catch (\Exception $e) {
            // Table might not exist yet
        }
        
        try {
            DB::statement('TRUNCATE TABLE user_balances');
        } catch (\Exception $e) {
            // Table might not exist yet
        }
    }

    private function createBalanceTables()
    {
        // user_balances
        if (!Schema::hasTable('user_balances')) {
            Schema::create('user_balances', function ($table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unique();
                $table->decimal('balance', 15, 2)->default(0);
                $table->decimal('total_recharged', 15, 2)->default(0);
                $table->decimal('total_spent', 15, 2)->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // user_balance_transactions
        if (!Schema::hasTable('user_balance_transactions')) {
            Schema::create('user_balance_transactions', function ($table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id');
                $table->string('transaction_type');
                $table->string('service_type')->nullable();
                $table->string('reference_model')->nullable();
                $table->bigInteger('reference_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->decimal('balance_before', 15, 2);
                $table->decimal('balance_after', 15, 2);
                $table->text('description')->nullable();
                $table->string('status')->default('completed');
                $table->timestamps();
            });
        }

        // user_recharges
        if (!Schema::hasTable('user_recharges')) {
            Schema::create('user_recharges', function ($table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id');
                $table->decimal('amount', 15, 2);
                $table->string('status')->default('completed');
                $table->timestamps();
            });
        }
    }

    /**
     * Test: Single charge should update both tables atomically
     */
    public function test_charge_service_updates_both_tables()
    {
        $amount = 50000;

        // Charge
        $transaction = BalanceService::chargeService(
            $this->user->id,
            $amount,
            'vps',
            'Test VPS usage',
            'VpsUsage',
            1
        );

        // Verify transaction record created
        $this->assertDatabaseHas('user_balance_transactions', [
            'user_id' => $this->user->id,
            'amount' => -$amount,
            'status' => 'completed',
        ]);

        // Verify balance updated
        $this->balance->refresh();
        $this->assertEquals(1000000 - $amount, $this->balance->balance);
        $this->assertEquals($amount, $this->balance->total_spent);
    }

    /**
     * Test: Insufficient balance should throw exception (all-or-nothing)
     */
    public function test_charge_fails_with_insufficient_balance()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Số dư không đủ');

        // Try to charge more than balance
        BalanceService::chargeService($this->user->id, 2000000, 'vps');

        // Verify no transaction created
        $this->assertDatabaseMissing('user_balance_transactions', [
            'user_id' => $this->user->id,
            'transaction_type' => 'service_fee',
        ]);
    }

    /**
     * Test: Multiple concurrent charges (pessimistic lock)
     */
    public function test_concurrent_charges_dont_overdraw()
    {
        // Simulate 5 concurrent requests, each trying to charge 250000
        // Total = 1250000 (exceeds balance of 1000000)
        // Expected: 4 succeed, 1 fails

        $results = [];
        for ($i = 0; $i < 5; $i++) {
            try {
                $result = BalanceService::chargeService(
                    $this->user->id,
                    250000,
                    'vps',
                    "Charge {$i}"
                );
                $results['success'][] = $result->id;
            } catch (\Exception $e) {
                $results['failed'][] = $e->getMessage();
            }
        }

        // Verify final balance is non-negative
        $this->balance->refresh();
        $this->assertGreaterThanOrEqual(0, $this->balance->balance);

        // Verify transactions sum correctly
        $transactionTotal = UserBalanceTransaction::where('user_id', $this->user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $this->assertEquals($transactionTotal, $this->balance->balance);
    }

    /**
     * Test: Verify sync detects and reports discrepancies
     */
    public function test_verify_sync_detects_mismatch()
    {
        // Create a transaction
        BalanceService::chargeService($this->user->id, 100000, 'vps');

        // Manually break sync (simulate corruption)
        $this->balance->update(['balance' => 999999]);

        // Run verification
        $results = BalanceService::verifyAndFixBalance($this->user->id);

        // Should detect mismatch
        $this->assertEquals(1, $results['checked']);
        $this->assertEquals(1, $results['fixed']);

        // Verify fixed
        $this->balance->refresh();
        $this->assertEquals(900000, $this->balance->balance);
    }

    /**
     * Test: Balance before/after values are correct
     */
    public function test_transaction_balance_before_after_correct()
    {
        $balanceBefore = $this->balance->balance;
        $chargeAmount = 123456;

        BalanceService::chargeService($this->user->id, $chargeAmount, 'vps');

        $transaction = UserBalanceTransaction::where('user_id', $this->user->id)->latest()->first();

        $this->assertEquals($balanceBefore, $transaction->balance_before);
        $this->assertEquals($balanceBefore - $chargeAmount, $transaction->balance_after);
        $this->assertEquals(-$chargeAmount, $transaction->amount);
    }

    /**
     * Test: Recharge updates both tables correctly
     */
    public function test_recharge_updates_both_tables()
    {
        $rechargeAmount = 500000;

        BalanceService::completeRecharge(
            BalanceService::createRecharge($this->user->id, $rechargeAmount)->id
        );

        $this->balance->refresh();
        $this->assertEquals(1000000 + $rechargeAmount, $this->balance->balance);
        $this->assertEquals(1000000 + $rechargeAmount, $this->balance->total_recharged);

        // Verify transaction created
        $this->assertDatabaseHas('user_balance_transactions', [
            'user_id' => $this->user->id,
            'transaction_type' => 'recharge',
            'amount' => $rechargeAmount,
        ]);
    }

    /**
     * Test: Rebuild all balances from transactions
     */
    public function test_rebuild_all_balances()
    {
        // Create multiple transactions
        BalanceService::chargeService($this->user->id, 100000, 'vps');
        BalanceService::chargeService($this->user->id, 50000, 'hosting');

        // Corrupt the balance
        $this->balance->update(['balance' => 0, 'total_spent' => 0]);

        // Rebuild
        $results = BalanceService::rebuildAllBalances();

        $this->balance->refresh();
        $this->assertEquals(1000000 - 150000, $this->balance->balance);
        $this->assertEquals(150000, $this->balance->total_spent);
    }

    /**
     * Test: Transaction rollback on error
     */
    public function test_transaction_rollback_on_error()
    {
        // Start a transaction externally that will cause conflict
        // (This tests DB::transaction atomicity)

        $countBefore = UserBalanceTransaction::count();

        try {
            BalanceService::chargeService($this->user->id, 2000000, 'vps');
        } catch (\Exception $e) {
            // Expected
        }

        // Verify no partial data
        $this->assertEquals($countBefore, UserBalanceTransaction::count());
        $this->balance->refresh();
        $this->assertEquals(1000000, $this->balance->balance);
    }

    /**
     * Test: Get discrepancy report
     */
    public function test_get_balance_discrepancies_report()
    {
        // Create clean transaction
        BalanceService::chargeService($this->user->id, 100000, 'vps');

        // Corrupt another user's balance
        $user2 = User::factory()->create();
        $balance2 = UserBalance::create([
            'user_id' => $user2->id,
            'balance' => 500000,
            'total_recharged' => 500000,
        ]);
        UserBalanceTransaction::create([
            'user_id' => $user2->id,
            'transaction_type' => 'service_fee',
            'amount' => -100000,
            'balance_before' => 500000,
            'balance_after' => 400000,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);
        $balance2->update(['balance' => 500000]); // Corrupt

        $discrepancies = BalanceService::getBalanceDiscrepancies();

        $this->assertGreaterThan(0, count($discrepancies));
        $this->assertTrue(
            collect($discrepancies)->pluck('user_id')->contains($user2->id)
        );
    }
}
