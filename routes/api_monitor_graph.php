<?php

/**
 * Monitor Graph API Routes
 *
 * Add this to your routes/api.php file:
 *
 * require __DIR__ . '/api_monitor_graph.php';
 */

use Illuminate\Support\Facades\Route;

Route::prefix('monitor-graph')->group(function () {

    // Get uptime/downtime status graph
    // GET /api/monitor-graph/uptime?monitor_id=1&period=24h
    Route::get('uptime', [\App\Http\ControllerApi\MonitorGraphControllerApi::class, 'uptime']);

    Route::get('uptime-list', [\App\Http\ControllerApi\MonitorGraphControllerApi::class, 'uptimeList']);

    // Get response time graph
    // GET /api/monitor-graph/response-time?monitor_id=1&period=7d
    Route::get('response-time', [\App\Http\ControllerApi\MonitorGraphControllerApi::class, 'responseTime']);

    // Get system metrics graph
    // GET /api/monitor-graph/system-metrics?metric_type=cpu_usage&period=30d
    Route::get('system-metrics', [\App\Http\ControllerApi\MonitorGraphControllerApi::class, 'systemMetrics']);

    // Get available metric types
    // GET /api/monitor-graph/metric-types
    Route::get('metric-types', [\App\Http\ControllerApi\MonitorGraphControllerApi::class, 'metricTypes']);
});
