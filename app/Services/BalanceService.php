<?php

namespace App\Services;

use App\Models\UserBalance;
use App\Models\UserBalanceTransaction;
use App\Models\BalanceSuspensionLog;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    /**
     * Tạo nạp tiền mới
     */
    public static function createRecharge($userId, $amount, $paymentMethod = 'bank_transfer')
    {
        $userBalance = UserBalance::where('user_id', $userId)->first();
        if (!$userBalance) {
            throw new \Exception("User balance not found for user_id: $userId");
        }

        if ($userBalance->isFrozen()) {
            throw new \Exception("Tài khoản đang bị khóa");
        }

        return \App\Models\UserRecharge::create([
            'user_id' => $userId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
        ]);
    }

    /**
     * Hoàn tất nạp tiền
     */
    public static function completeRecharge($rechargeId, $gatewayResponse = null)
    {
        return DB::transaction(function () use ($rechargeId, $gatewayResponse) {
            $recharge = \App\Models\UserRecharge::findOrFail($rechargeId);
            
            if ($recharge->status == 'completed') {
                throw new \Exception("Giao dịch đã được hoàn tất trước đó");
            }

            // Update recharge
            $recharge->markAsCompleted($gatewayResponse);

            // Create transaction
            $transaction = UserBalanceTransaction::createTransaction(
                userId: $recharge->user_id,
                transactionType: 'recharge',
                amount: $recharge->amount,
                description: "Nạp tiền qua {$recharge->payment_method}"
            );
            
            // Link to recharge
            $transaction->update(['related_recharge_id' => $recharge->id]);

            // Update user_balance
            $userBalance = UserBalance::where('user_id', $recharge->user_id)->first();
            $userBalance->update([
                'balance' => $userBalance->balance + $recharge->amount,
                'total_recharged' => $userBalance->total_recharged + $recharge->amount,
                'last_transaction_at' => now(),
            ]);

            // Gỡ suspension nếu có
            self::resumeServicesIfEligible($recharge->user_id);

            return $recharge;
        });
    }

    /**
     * Trừ tiền dịch vụ (với locking để tránh race condition)
     * 
     * ATOMIC FLOW:
     * 1. Lock user_balance row (pessimistic lock)
     * 2. Validate balance có đủ không
     * 3. INSERT transaction record (chi tiết)
     * 4. UPDATE user_balance (tóm tắt)
     * 5. Check & suspend nếu cần
     * 6. Unlock + commit (tất cả hoặc không)
     */
    public static function chargeService($userId, $amount, $serviceType, $description = null, $referenceModel = null, $referenceId = null)
    {
        return DB::transaction(function () use ($userId, $amount, $serviceType, $description, $referenceModel, $referenceId) {
            // LOCK: Prevent concurrent charges (skip for SQLite as it has poor lock support)
            $userBalance = UserBalance::where('user_id', $userId);
            
            // Only use lockForUpdate for non-SQLite databases
            if (DB::getDriverName() !== 'sqlite') {
                $userBalance = $userBalance->lockForUpdate();
            }
            
            $userBalance = $userBalance->firstOrFail();

            // VALIDATE: Balance có đủ không
            $balanceBefore = $userBalance->balance;
            if ($balanceBefore < $amount) {
                throw new \Exception(
                    "Số dư không đủ. Có: " . number_format($balanceBefore, 0) . " VND, " .
                    "Cần: " . number_format($amount, 0) . " VND"
                );
            }

            $balanceAfter = $balanceBefore - $amount;

            // STEP 1: Create detailed transaction record (source of truth for audit)
            $transaction = UserBalanceTransaction::create([
                'user_id' => $userId,
                'transaction_type' => 'service_fee',
                'service_type' => $serviceType,
                'reference_model' => $referenceModel,
                'reference_id' => $referenceId,
                'amount' => -$amount,  // Negative = debit/spend
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description ?? "Chi phí dịch vụ {$serviceType}",
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            // STEP 2: Update denormalized summary (fast cache)
            $userBalance->update([
                'balance' => $balanceAfter,
                'total_spent' => $userBalance->total_spent + $amount,
                'last_transaction_at' => now(),
            ]);

            // STEP 3: Check & suspend if needed
            self::checkAndSuspendIfNeeded($userId);

            // ✅ Transaction auto-commits here (all-or-nothing)
            return $transaction;
        });
    }

    /**
     * Kiểm tra và tạm dừng dịch vụ nếu dư tiền thấp
     */
    public static function checkAndSuspendIfNeeded($userId)
    {
        $userBalance = UserBalance::where('user_id', $userId)->first();
        if (!$userBalance) return;

        // Kiểm tra đã tạm dừng chưa
        $activeSuspension = BalanceSuspensionLog::where('user_id', $userId)
            ->whereNull('resumed_at')
            ->first();

        if ($activeSuspension) {
            return;  // Đã tạm dừng rồi
        }

        // Nếu dư tiền âm hoặc thấp hơn ngưỡng
        if ($userBalance->balance < 0) {
            self::suspendServices($userId, 'Số dư âm - không đủ tiền');
        }
    }

    /**
     * Tạm dừng dịch vụ
     */
    public static function suspendServices($userId, $reason = 'Số dư không đủ')
    {
        $userBalance = UserBalance::where('user_id', $userId)->first();
        if (!$userBalance) return;

        // Create suspension log
        BalanceSuspensionLog::create([
            'user_id' => $userId,
            'reason' => $reason,
            'suspended_at' => now(),
            'balance_at_suspension' => $userBalance->balance,
        ]);

        // TODO: Dừng các VpsInstance của user này
        // \App\Models\VpsInstance::where('user_id', $userId)->update(['power_state' => 'powered_off']);

        // TODO: Gửi email thông báo
    }

    /**
     * Gỡ tạm dừng dịch vụ nếu đủ điều kiện
     */
    public static function resumeServicesIfEligible($userId)
    {
        $userBalance = UserBalance::where('user_id', $userId)->first();
        if (!$userBalance || $userBalance->balance <= 0) {
            return;  // Chưa đủ tiền
        }

        // Gỡ suspension
        $suspension = BalanceSuspensionLog::where('user_id', $userId)
            ->whereNull('resumed_at')
            ->first();

        if ($suspension) {
            $suspension->resume();

            // TODO: Bật các VpsInstance của user này
            // \App\Models\VpsInstance::where('user_id', $userId)->update(['power_state' => 'powered_on']);

            // TODO: Gửi email thông báo
        }
    }

    /**
     * Lấy thông tin dư tiền
     */
    public static function getBalanceInfo($userId)
    {
        $userBalance = UserBalance::where('user_id', $userId)->first();
        if (!$userBalance) return null;

        $activeServices = BalanceSuspensionLog::where('user_id', $userId)
            ->whereNull('resumed_at')
            ->count();

        return [
            'balance' => $userBalance->balance,
            'total_recharged' => $userBalance->total_recharged,
            'total_spent' => $userBalance->total_spent,
            'is_frozen' => $userBalance->is_frozen,
            'is_suspended' => $activeServices > 0,
            'low_balance_threshold' => $userBalance->low_balance_threshold,
        ];
    }

    /**
     * Lấy lịch sử giao dịch
     */
    public static function getTransactionHistory($userId, $limit = 50)
    {
        return UserBalanceTransaction::where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy lịch sử nạp tiền
     */
    public static function getRechargeHistory($userId, $limit = 20)
    {
        return \App\Models\UserRecharge::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * ========================================
     * VERIFY & AUDIT METHODS (for sync)
     * ========================================
     */

    /**
     * Kiểm tra & sửa desync giữa user_balances vs user_balance_transactions
     * 
     * Chạy hàng ngày (nightly job) để detect & fix inconsistencies
     */
    public static function verifyAndFixBalance($userId = null)
    {
        $query = UserBalance::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $results = [
            'checked' => 0,
            'synced' => 0,
            'fixed' => 0,
            'errors' => [],
            'details' => [],
        ];

        foreach ($query->get() as $userBalance) {
            $results['checked']++;
            
            // Calculate actual balance from transactions + initial amount
            // The actual balance = sum of all transactions (recharges are positive, charges are negative)
            $actualBalance = UserBalanceTransaction::where('user_id', $userBalance->user_id)
                ->where('status', 'completed')
                ->sum('amount');

            $storedBalance = $userBalance->balance;
            $discrepancy = $actualBalance - $storedBalance;

            if ($discrepancy == 0) {
                // ✅ SYNCED
                $results['synced']++;
                $results['details'][] = [
                    'user_id' => $userBalance->user_id,
                    'status' => 'OK',
                    'balance' => $storedBalance,
                ];
            } else {
                // ⚠️ DESYNC DETECTED
                $results['details'][] = [
                    'user_id' => $userBalance->user_id,
                    'status' => 'MISMATCH',
                    'stored_balance' => $storedBalance,
                    'actual_balance' => $actualBalance,
                    'discrepancy' => $discrepancy,
                ];

                // Try to FIX
                try {
                    DB::transaction(function () use ($userBalance, $actualBalance) {
                        $userBalance->update(['balance' => $actualBalance]);
                    });
                    $results['fixed']++;
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'user_id' => $userBalance->user_id,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Rebuild user_balance.balance từ transactions (full recalculation)
     * 
     * Cách an toàn hơn: tính toàn bộ lại từ transactions
     */
    public static function rebuildAllBalances()
    {
        // Lấy tất cả users có transaction
        $userIds = UserBalanceTransaction::where('status', 'completed')
            ->distinct('user_id')
            ->pluck('user_id');

        $results = [
            'rebuilt' => 0,
            'errors' => [],
        ];

        foreach ($userIds as $userId) {
            try {
                DB::transaction(function () use ($userId) {
                    // Calculate totals
                    $transactions = UserBalanceTransaction::where('user_id', $userId)
                        ->where('status', 'completed')
                        ->get();

                    $totalRecharged = $transactions
                        ->where('transaction_type', 'recharge')
                        ->sum('amount');

                    $totalSpent = $transactions
                        ->where('transaction_type', '!=', 'recharge')
                        ->sum(function ($t) {
                            return abs($t->amount);  // Sum absolute values for spending
                        });

                    $currentBalance = $totalRecharged - $totalSpent;

                    // Update balance
                    UserBalance::where('user_id', $userId)
                        ->update([
                            'balance' => $currentBalance,
                            'total_recharged' => $totalRecharged,
                            'total_spent' => $totalSpent,
                        ]);
                });

                $results['rebuilt']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get balance discrepancy report
     * 
     * Dùng để tìm users nào bị desync
     */
    public static function getBalanceDiscrepancies()
    {
        $discrepancies = [];

        $users = UserBalance::get();
        foreach ($users as $userBalance) {
            // Calculate from transactions
            $actualBalance = UserBalanceTransaction::where('user_id', $userBalance->user_id)
                ->where('status', 'completed')
                ->sum('amount');

            $storedBalance = $userBalance->balance;
            
            if ($actualBalance != $storedBalance) {
                $discrepancies[] = [
                    'user_id' => $userBalance->user_id,
                    'stored' => $storedBalance,
                    'actual' => $actualBalance,
                    'difference' => abs($actualBalance - $storedBalance),
                ];
            }
        }

        // Sort by largest discrepancy
        usort($discrepancies, fn($a, $b) => $b['difference'] <=> $a['difference']);

        return $discrepancies;
    }
}
