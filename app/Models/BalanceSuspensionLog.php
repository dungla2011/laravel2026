<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BalanceSuspensionLog extends ModelGlxBase
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'balance_at_suspension' => 'decimal:2',
        'suspended_at' => 'datetime',
        'resumed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Kiểm tra hiện tại bị tạm dừng không
     */
    public function isActive()
    {
        return $this->resumed_at === null;
    }

    /**
     * Gỡ tạm dừng
     */
    public function resume()
    {
        $this->update([
            'resumed_at' => now(),
            'duration_minutes' => $this->suspended_at->diffInMinutes(now()),
        ]);
    }
}
