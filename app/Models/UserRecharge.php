<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRecharge extends ModelGlxBase
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'expired_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transaction()
    {
        return $this->hasOne(UserBalanceTransaction::class, 'related_recharge_id', 'id');
    }

    /**
     * Kiểm tra giao dịch còn hiệu lực không
     */
    public function isExpired()
    {
        if ($this->expired_at === null) return false;
        return now()->greaterThan($this->expired_at);
    }

    /**
     * Đánh dấu giao dịch thành công
     */
    public function markAsCompleted($gatewayResponse = null)
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'completed_at' => now(),
            'gateway_response' => $gatewayResponse,
        ]);
    }
}
