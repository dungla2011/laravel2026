<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBalanceTransaction extends ModelGlxBase
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function recharge()
    {
        return $this->belongsTo(UserRecharge::class, 'related_recharge_id', 'id');
    }

    /**
     * Tạo transaction mới
     */
    public static function createTransaction($userId, $transactionType, $amount, $description = null, $serviceType = null, $referenceModel = null, $referenceId = null)
    {
        $balance = UserBalance::where('user_id', $userId)->first();
        if (!$balance) {
            throw new \Exception("User balance not found for user_id: $userId");
        }

        $balanceBefore = $balance->balance;
        $balanceAfter = $balanceBefore + $amount;  // amount có thể âm (trừ tiền)

        return static::create([
            'user_id' => $userId,
            'transaction_type' => $transactionType,
            'service_type' => $serviceType,
            'reference_model' => $referenceModel,
            'reference_id' => $referenceId,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);
    }

    /**
     * Hủy giao dịch (hoàn lại tiền)
     */
    public function reverse($reason = null)
    {
        if ($this->is_reversed) {
            throw new \Exception("Transaction already reversed");
        }

        // Tạo transaction hoàn lại
        $reverseAmount = -$this->amount;  // Đảo dấu
        
        static::create([
            'user_id' => $this->user_id,
            'transaction_type' => 'refund',
            'amount' => $reverseAmount,
            'balance_before' => $this->balance_after,
            'balance_after' => $this->balance_after + $reverseAmount,
            'description' => "Hoàn lại giao dịch #{$this->id}: {$this->description}",
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        // Cập nhật transaction gốc
        $this->update([
            'is_reversed' => true,
            'reversed_at' => now(),
            'reversed_reason' => $reason,
        ]);

        // Update user_balance
        $balance = UserBalance::where('user_id', $this->user_id)->first();
        $balance->update([
            'balance' => $balance->balance + $reverseAmount,
            'total_spent' => max(0, $balance->total_spent - $this->amount),
        ]);
    }

    /**
     * Lấy tổng chi tiêu theo dịch vụ
     */
    public static function getTotalSpentByService($userId)
    {
        return static::where('user_id', $userId)
            ->where('transaction_type', 'service_fee')
            ->whereNull('deleted_at')
            ->groupBy('service_type')
            ->selectRaw('service_type, SUM(ABS(amount)) as total')
            ->get()
            ->keyBy('service_type')
            ->map(function($item) { return $item->total; })
            ->toArray();
    }
}
