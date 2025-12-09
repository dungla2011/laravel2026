<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VpsUsageSummary extends Model
{
    protected $table = 'vps_usage_summaries';
    
    protected $fillable = [
        'user_id',
        'instance_id',
        'period_start',
        'period_type',
        'total_records',
        'avg_price_per_minute',
        'total_price',
        'power_state',
        'avg_number_ip_address',
    ];
    
    protected $casts = [
        'period_start' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
