<?php

namespace App\Http\ControllerApi;

use App\Models\MonitorItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitorGraphControllerApi extends BaseApiController
{
    /**
     * Get uptime/downtime status graph data
     * GET /api/monitor-graph/uptime?monitor_id=1&period=24h
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uptime(Request $request)
    {
        $monitorId = $request->input('monitor_id');
//        $monitorId = 25;
        $period = $request->input('period', '24h'); // 24h, 7d, 30d, 90d

        if (!$monitorId) {
            return response()->json(['error' => 'monitor_id is required'], 400);
        }

        $objUser = getCurrentUserId(1);
        $uid = $objUser->getId();

        //Kiểm tra xem có quyền xem monitor này không
        $mon = MonitorItem::find($monitorId);
        if (!$mon) {
            return response()->json(['error' => 'Monitor not found'], 404);
        }
        if ($mon->user_id != $uid ) {
            return response()->json(['error' => " No permission to view this monitor $mon->user_id != $uid"], 403);
        }

        $hours = $this->parsePeriod($period);
        $startTime = Carbon::now()->subHours($hours);

        // Get check results with time intervals
        $checks = DB::table('monitor_checks')
            ->select(
                $this->getTimeBucketSelect('time'),
                DB::raw('AVG(CASE WHEN status = 1 THEN 1 ELSE 0 END) as uptime_ratio'),
                DB::raw('COUNT(*) as total_checks'),
                DB::raw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as success_count'),
                DB::raw('SUM(CASE WHEN status = -1 THEN 1 ELSE 0 END) as failed_count')
            )
            ->where('monitor_id', $monitorId)
            ->where('time', '>=', $startTime)
            ->groupBy('time_bucket')
            ->orderBy('time_bucket', 'asc')
            ->get();

        // Calculate statistics
        $totalChecks = $checks->sum('total_checks');
        $totalSuccess = $checks->sum('success_count');
        $totalFailed = $checks->sum('failed_count');
        $uptimePercentage = $totalChecks > 0 ? round(($totalSuccess / $totalChecks) * 100, 2) : 0;

        // Format data for Chart.js
        $labels = [];
        $statusData = []; // 1 = up, 0 = down
        $uptimeData = []; // percentage per bucket

        foreach ($checks as $check) {
            $labels[] = Carbon::parse($check->time_bucket)->format('Y-m-d H:i');
            $statusData[] = $check->uptime_ratio >= 0.5 ? 1 : 0; // Consider up if >50% success
            $uptimeData[] = round($check->uptime_ratio * 100, 2);
        }

        return response()->json([
            'success' => true,
            'monitor_id' => $monitorId,
            'period' => $period,
            'stats' => [
                'uptime_percentage' => $uptimePercentage,
                'total_checks' => $totalChecks,
                'successful_checks' => $totalSuccess,
                'failed_checks' => $totalFailed,
            ],
            'chart_data' => [
                'labels' => $labels,
                'status' => $statusData, // For status bar chart
                'uptime_percentage' => $uptimeData, // For line chart
            ],
        ]);
    }

    /**
     * Get uptime list for all enabled monitors
     * GET /api/monitor-graph/uptime-list?period=24h
     *
     * Returns uptime data for all monitors owned by current user that are enabled
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uptimeList(Request $request)
    {
        $period = $request->input('period', '24h'); // 24h, 7d, 30d, 90d
        
        $objUser = getCurrentUserId(1);
        $uid = $objUser->getId();

        // Get all enabled monitors for this user
        $monitors = MonitorItem::where('user_id', $uid)
            ->where('enable', 1)
            ->orderBy('last_check_status', 'asc') // Lỗi lên trước
            ->orderBy('name', 'asc')
            ->get();

        if ($monitors->isEmpty()) {
            return response()->json([
                'success' => true,
                'period' => $period,
                'total_monitors' => 0,
                'monitors' => [],
            ]);
        }

        $hours = $this->parsePeriod($period);
        $startTime = Carbon::now()->subHours($hours);

        $result = [];

        foreach ($monitors as $monitor) {
            // Get check results for this monitor
            $checks = DB::table('monitor_checks')
                ->select(
                    $this->getTimeBucketSelect('time'),
                    DB::raw('AVG(CASE WHEN status = 1 THEN 1 ELSE 0 END) as uptime_ratio'),
                    DB::raw('COUNT(*) as total_checks'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as success_count'),
                    DB::raw('SUM(CASE WHEN status = -1 THEN 1 ELSE 0 END) as failed_count')
                )
                ->where('monitor_id', $monitor->id)
                ->where('time', '>=', $startTime)
                ->groupBy('time_bucket')
                ->orderBy('time_bucket', 'asc')
                ->get();

            // Calculate statistics
            $totalChecks = $checks->sum('total_checks');
            $totalSuccess = $checks->sum('success_count');
            $totalFailed = $checks->sum('failed_count');
            $uptimePercentage = $totalChecks > 0 ? round(($totalSuccess / $totalChecks) * 100, 2) : 0;

            // Format data for Chart.js
            $labels = [];
            $statusData = [];
            $uptimeData = [];

            foreach ($checks as $check) {
                $labels[] = Carbon::parse($check->time_bucket)->format('Y-m-d H:i');
                $statusData[] = $check->uptime_ratio >= 0.5 ? 1 : 0;
                $uptimeData[] = round($check->uptime_ratio * 100, 2);
            }

            $result[] = [
                'monitor_id' => $monitor->id,
                'monitor_name' => $monitor->name,
                'monitor_url' => $monitor->url,
                'monitor_type' => $monitor->type,
                'last_check_status' => $monitor->last_check_status,
                'stats' => [
                    'uptime_percentage' => $uptimePercentage,
                    'total_checks' => $totalChecks,
                    'successful_checks' => $totalSuccess,
                    'failed_checks' => $totalFailed,
                ],
                'chart_data' => [
                    'labels' => $labels,
                    'status' => $statusData,
                    'uptime_percentage' => $uptimeData,
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'period' => $period,
            'total_monitors' => count($result),
            'monitors' => $result,
        ]);
    }

    /**
     * Get response time graph data
     * GET /api/monitor-graph/response-time?monitor_id=1&period=24h
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseTime(Request $request)
    {
        die("Not support yet 1");
        $monitorId = $request->input('monitor_id');
        $period = $request->input('period', '24h');

        if (!$monitorId) {
            return response()->json(['error' => 'monitor_id is required'], 400);
        }

        $hours = $this->parsePeriod($period);
        $startTime = Carbon::now()->subHours($hours);

        // Get response time data
        $checks = DB::table('monitor_checks')
            ->select(
                $this->getTimeBucketSelect('time'),
                DB::raw('AVG(response_time) as avg_response_time'),
                DB::raw('MIN(response_time) as min_response_time'),
                DB::raw('MAX(response_time) as max_response_time'),
                'status'
            )
            ->where('monitor_id', $monitorId)
            ->where('time', '>=', $startTime)
            ->whereNotNull('response_time')
            ->groupBy('time_bucket', 'status')
            ->orderBy('time_bucket', 'asc')
            ->get();

        // Calculate statistics
        $avgResponseTime = round($checks->avg('avg_response_time'), 2);
        $minResponseTime = round($checks->min('min_response_time'), 2);
        $maxResponseTime = round($checks->max('max_response_time'), 2);

        // Format data for Chart.js
        $labels = [];
        $avgData = [];
        $minData = [];
        $maxData = [];
        $statusColors = [];

        foreach ($checks as $check) {
            $time = Carbon::parse($check->time_bucket)->format('Y-m-d H:i');
            if (!in_array($time, $labels)) {
                $labels[] = $time;
            }

            $avgData[] = round($check->avg_response_time, 2);
            $minData[] = round($check->min_response_time, 2);
            $maxData[] = round($check->max_response_time, 2);
            $statusColors[] = $check->status == 1 ? 'rgba(75, 192, 192, 0.6)' : 'rgba(255, 99, 132, 0.6)';
        }

        return response()->json([
            'success' => true,
            'monitor_id' => $monitorId,
            'period' => $period,
            'stats' => [
                'avg_response_time' => $avgResponseTime,
                'min_response_time' => $minResponseTime,
                'max_response_time' => $maxResponseTime,
            ],
            'chart_data' => [
                'labels' => $labels,
                'avg' => $avgData,
                'min' => $minData,
                'max' => $maxData,
                'status_colors' => $statusColors,
            ],
        ]);
    }

    /**
     * Get system metrics graph data
     * GET /api/monitor-graph/system-metrics?metric_type=cpu_usage&period=24h
     *
     * Use cases for monitor_system_metrics:
     * - CPU usage over time
     * - Memory usage trends
     * - Disk I/O performance
     * - Network bandwidth
     * - Database connection pool
     * - Application-specific metrics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function systemMetrics(Request $request)
    {
        die("Not support yet 2");
        $metricType = $request->input('metric_type'); // cpu_usage, memory_usage, disk_io, etc.
        $period = $request->input('period', '24h');
        $tags = $request->input('tags', []); // Filter by tags if needed

        if (!$metricType) {
            return response()->json(['error' => 'metric_type is required'], 400);
        }

        $hours = $this->parsePeriod($period);
        $startTime = Carbon::now()->subHours($hours);

        // Get metric data
        $query = DB::table('monitor_system_metrics')
            ->select(
                $this->getTimeBucketSelect('time'),
                DB::raw('AVG(value) as avg_value'),
                DB::raw('MIN(value) as min_value'),
                DB::raw('MAX(value) as max_value')
            )
            ->where('metric_type', $metricType)
            ->where('time', '>=', $startTime);

        // Apply tags filter if provided
        if (!empty($tags)) {
            foreach ($tags as $key => $value) {
                $query->whereRaw("JSON_EXTRACT(tags, '$.\"{$key}\"') = ?", [$value]);
            }
        }

        $metrics = $query->groupBy('time_bucket')
            ->orderBy('time_bucket', 'asc')
            ->get();

        // Calculate statistics
        $avgValue = round($metrics->avg('avg_value'), 2);
        $minValue = round($metrics->min('min_value'), 2);
        $maxValue = round($metrics->max('max_value'), 2);

        // Format data for Chart.js
        $labels = [];
        $avgData = [];
        $minData = [];
        $maxData = [];

        foreach ($metrics as $metric) {
            $labels[] = Carbon::parse($metric->time_bucket)->format('Y-m-d H:i');
            $avgData[] = round($metric->avg_value, 2);
            $minData[] = round($metric->min_value, 2);
            $maxData[] = round($metric->max_value, 2);
        }

        return response()->json([
            'success' => true,
            'metric_type' => $metricType,
            'period' => $period,
            'stats' => [
                'avg_value' => $avgValue,
                'min_value' => $minValue,
                'max_value' => $maxValue,
            ],
            'chart_data' => [
                'labels' => $labels,
                'avg' => $avgData,
                'min' => $minData,
                'max' => $maxData,
            ],
        ]);
    }

    /**
     * Get available metric types from database
     * GET /api/monitor-graph/metric-types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function metricTypes()
    {
        die("Not support yet 13");
        $types = DB::table('monitor_system_metrics')
            ->select('metric_type', DB::raw('COUNT(*) as count'))
            ->groupBy('metric_type')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'metric_types' => $types,
        ]);
    }

    /**
     * Parse period string to hours
     *
     * @param string $period
     * @return int
     */
    private function parsePeriod($period)
    {
//        die("Not support yet 14");
        $matches = [];
        // Support format: 30m, 1h, 7d, 1w, 1M (months), 1y
        if (preg_match('/^(\d+)(m|h|d|w|M|y)$/i', $period, $matches)) {
            $value = (int)$matches[1];
            $unit = $matches[2]; // Keep case-sensitive for 'm' vs 'M'

            switch ($unit) {
                case 'm': return $value / 60; // minutes to hours
                case 'h': return $value;
                case 'd': return $value * 24;
                case 'w': return $value * 24 * 7;
                case 'M': return $value * 24 * 30; // months (uppercase M)
                case 'y': return $value * 24 * 365;
            }
        }

        return 24; // Default 24 hours
    }

    /**
     * Get time bucket SQL for different database drivers
     *
     * @param string $column
     * @return \Illuminate\Database\Query\Expression
     */
    private function getTimeBucketSelect($column = 'time')
    {

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: TO_CHAR(time, 'YYYY-MM-DD HH24:MI:00')
            return DB::raw("TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:00') as time_bucket");
        } else {
            // MySQL: DATE_FORMAT(time, '%Y-%m-%d %H:%i:00')
            return DB::raw("DATE_FORMAT({$column}, '%Y-%m-%d %H:%i:00') as time_bucket");
        }
    }
}
