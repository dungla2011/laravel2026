<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBalance extends ModelGlxBase
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'balance' => 'decimal:2',
        'total_recharged' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'low_balance_threshold' => 'decimal:2',
        'last_low_balance_alert' => 'datetime',
        'last_transaction_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function recharges()
    {
        return $this->hasMany(UserRecharge::class, 'user_id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(UserBalanceTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Kiểm tra dư tiền đủ không
     */
    public function hasEnoughBalance($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Kiểm tra account bị freeze không
     */
    public function isFrozen()
    {
        return $this->is_frozen || $this->status != 1;
    }
}
