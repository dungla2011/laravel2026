<?php

namespace App\Services;

use App\Models\VpsUsage;
use App\Models\VpsUsageSummary;
use Illuminate\Support\Facades\DB;

class VpsUsageAggregationService
{
    /**
     * Aggregate hourly data từ vps_usages vào vps_usage_summaries
     * Chạy mỗi giờ hoặc mỗi 10 phút tùy theo yêu cầu
     */
    public function aggregateHourly($hoursBack = 1)
    {
        $now = now();
        $startTime = $now->copy()->subHours($hoursBack)->startOfHour();
        $endTime = $now->copy()->startOfHour();
        
        // Query raw để tối ưu hiệu suất
        $results = DB::table('vps_usages')
            ->whereBetween('timestamp_minute', [$startTime, $endTime])
            ->whereNull('deleted_at')
            ->selectRaw('
                user_id,
                instance_id,
                DATE_FORMAT(timestamp_minute, "%Y-%m-%d %H:00:00") as period_start,
                COUNT(*) as total_records,
                AVG(price_per_minute) as avg_price_per_minute,
                SUM(price_per_minute) as total_price,
                power_state,
                AVG(number_ip_address) as avg_number_ip_address
            ')
            ->groupBy('user_id', 'instance_id', 'period_start', 'power_state')
            ->get();
        
        foreach ($results as $row) {
            VpsUsageSummary::updateOrCreate(
                [
                    'user_id' => $row->user_id,
                    'instance_id' => $row->instance_id,
                    'period_start' => $row->period_start,
                    'period_type' => 'hourly',
                ],
                [
                    'total_records' => $row->total_records,
                    'avg_price_per_minute' => $row->avg_price_per_minute,
                    'total_price' => $row->total_price,
                    'power_state' => $row->power_state,
                    'avg_number_ip_address' => $row->avg_number_ip_address,
                ]
            );
        }
        
        return count($results);
    }
    
    /**
     * Aggregate daily data từ vps_usages
     * Chạy mỗi ngày lúc 00:01
     */
    public function aggregateDaily($daysBack = 1)
    {
        $now = now();
        $startTime = $now->copy()->subDays($daysBack)->startOfDay();
        $endTime = $now->copy()->startOfDay();
        
        $results = DB::table('vps_usages')
            ->whereBetween('timestamp_minute', [$startTime, $endTime])
            ->whereNull('deleted_at')
            ->selectRaw('
                user_id,
                instance_id,
                DATE_FORMAT(timestamp_minute, "%Y-%m-%d") as period_start,
                COUNT(*) as total_records,
                AVG(price_per_minute) as avg_price_per_minute,
                SUM(price_per_minute) as total_price,
                power_state,
                AVG(number_ip_address) as avg_number_ip_address
            ')
            ->groupBy('user_id', 'instance_id', 'period_start', 'power_state')
            ->get();
        
        foreach ($results as $row) {
            VpsUsageSummary::updateOrCreate(
                [
                    'user_id' => $row->user_id,
                    'instance_id' => $row->instance_id,
                    'period_start' => $row->period_start . ' 00:00:00',
                    'period_type' => 'daily',
                ],
                [
                    'total_records' => $row->total_records,
                    'avg_price_per_minute' => $row->avg_price_per_minute,
                    'total_price' => $row->total_price,
                    'power_state' => $row->power_state,
                    'avg_number_ip_address' => $row->avg_number_ip_address,
                ]
            );
        }
        
        return count($results);
    }
    
    /**
     * Cleanup dữ liệu cũ từ vps_usages (giữ 7 ngày gần đây)
     * Dữ liệu đã aggregate sẽ lưu ở vps_usage_summaries
     */
    public function cleanupOldData($daysToKeep = 7)
    {
        $cutoffDate = now()->subDays($daysToKeep)->startOfDay();
        
        $deleted = DB::table('vps_usages')
            ->where('timestamp_minute', '<', $cutoffDate)
            ->delete();
        
        return $deleted;
    }
}
