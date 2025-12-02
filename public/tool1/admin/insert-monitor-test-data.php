<?php
/**
 * Insert test data for monitor graphs
 * Run: php public/tool1/admin/insert-monitor-test-data.php
 */

$GLOBALS['DISABLE_DEBUG_BAR'] = 1;
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "/var/www/html/public/index.php";

use Illuminate\Support\Facades\DB;

echo "<h2>Insert Monitor Test Data</h2>\n";
echo "<pre>\n";

try {
    $now = now();
    $inserted = 0;
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "INSERTING monitor_checks DATA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Insert last 48 hours of monitor checks data
    for ($i = 0; $i < 48; $i++) {
        $time = $now->copy()->subHours($i);
        
        // Insert multiple checks per hour (every 5 minutes = 12 checks/hour)
        for ($j = 0; $j < 12; $j++) {
            $checkTime = $time->copy()->subMinutes($j * 5);
            
            // Monitor ID 1: Website (95% uptime)
            $status1 = (rand(1, 100) <= 95) ? 1 : -1;
            $responseTime1 = $status1 === 1 ? rand(50, 300) : null;
            
            DB::table('monitor_checks')->insert([
                'time' => $checkTime,
                'monitor_id' => 1,
                'check_type' => 'http',
                'status' => $status1,
                'response_time' => $responseTime1,
                'message' => $status1 === 1 ? 'OK' : 'Connection timeout',
                'details' => json_encode([
                    'url' => 'https://example.com',
                    'method' => 'GET',
                    'status_code' => $status1 === 1 ? 200 : 500
                ])
            ]);
            
            // Monitor ID 2: API (85% uptime)
            $status2 = (rand(1, 100) <= 85) ? 1 : -1;
            $responseTime2 = $status2 === 1 ? rand(100, 500) : null;
            
            DB::table('monitor_checks')->insert([
                'time' => $checkTime,
                'monitor_id' => 2,
                'check_type' => 'http',
                'status' => $status2,
                'response_time' => $responseTime2,
                'message' => $status2 === 1 ? 'API OK' : 'API Error',
                'details' => json_encode([
                    'url' => 'https://api.example.com/health',
                    'method' => 'GET',
                    'status_code' => $status2 === 1 ? 200 : 503
                ])
            ]);
            
            // Monitor ID 3: Database (99% uptime)
            $status3 = (rand(1, 100) <= 99) ? 1 : -1;
            $responseTime3 = $status3 === 1 ? rand(10, 100) : null;
            
            DB::table('monitor_checks')->insert([
                'time' => $checkTime,
                'monitor_id' => 3,
                'check_type' => 'tcp',
                'status' => $status3,
                'response_time' => $responseTime3,
                'message' => $status3 === 1 ? 'DB Connected' : 'DB Connection failed',
                'details' => json_encode([
                    'host' => 'db.example.com',
                    'port' => 5432,
                    'database' => 'production'
                ])
            ]);
            
            $inserted += 3;
        }
    }
    
    echo "✅ Inserted {$inserted} monitor_checks records\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "INSERTING monitor_system_metrics DATA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $metricsInserted = 0;
    
    // Insert last 48 hours of system metrics (every 5 minutes)
    for ($i = 0; $i < 48; $i++) {
        for ($j = 0; $j < 12; $j++) {
            $time = $now->copy()->subHours($i)->subMinutes($j * 5);
            
            // CPU Usage (40-80%)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'cpu_usage',
                'value' => rand(40, 80) + (rand(0, 99) / 100),
                'tags' => json_encode(['server' => 'web-01', 'core' => 'all'])
            ]);
            
            // Memory Usage (4-12 GB out of 16 GB)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'memory_usage',
                'value' => rand(4, 12) + (rand(0, 99) / 100),
                'tags' => json_encode(['server' => 'web-01', 'total' => 16, 'unit' => 'GB'])
            ]);
            
            // Disk I/O Read (20-80 MB/s)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'disk_io',
                'value' => rand(20, 80) + (rand(0, 99) / 100),
                'tags' => json_encode(['server' => 'web-01', 'operation' => 'read', 'disk' => 'sda1', 'unit' => 'MB/s'])
            ]);
            
            // Network Bandwidth Upload (50-200 Mbps)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'network_bandwidth',
                'value' => rand(50, 200) + (rand(0, 99) / 100),
                'tags' => json_encode(['server' => 'web-01', 'interface' => 'eth0', 'direction' => 'upload', 'unit' => 'Mbps'])
            ]);
            
            // Database Connections (20-80 out of 100)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'db_connections',
                'value' => rand(20, 80),
                'tags' => json_encode(['database' => 'main', 'pool_size' => 100])
            ]);
            
            // Active Users (800-2000)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'active_users',
                'value' => rand(800, 2000),
                'tags' => json_encode(['platform' => 'web'])
            ]);
            
            // Request Rate (500-1500 req/min)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'request_rate',
                'value' => rand(500, 1500) + (rand(0, 99) / 100),
                'tags' => json_encode(['endpoint' => '/api/*', 'unit' => 'req/min'])
            ]);
            
            // Error Rate (0.5-5%)
            DB::table('monitor_system_metrics')->insert([
                'time' => $time,
                'metric_type' => 'error_rate',
                'value' => rand(5, 50) / 10,
                'tags' => json_encode(['http_code' => '5xx', 'unit' => '%'])
            ]);
            
            $metricsInserted += 8;
        }
    }
    
    echo "✅ Inserted {$metricsInserted} monitor_system_metrics records\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "SUMMARY\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $totalChecks = DB::table('monitor_checks')->count();
    $totalMetrics = DB::table('monitor_system_metrics')->count();
    
    echo "📊 Total monitor_checks: {$totalChecks}\n";
    echo "📈 Total monitor_system_metrics: {$totalMetrics}\n\n";
    
    echo "Monitor IDs:\n";
    echo "  - ID 1: Website (95% uptime)\n";
    echo "  - ID 2: API (85% uptime)\n";
    echo "  - ID 3: Database (99% uptime)\n\n";
    
    echo "Available metric types:\n";
    $metricTypes = DB::table('monitor_system_metrics')
        ->select('metric_type', DB::raw('COUNT(*) as count'))
        ->groupBy('metric_type')
        ->orderBy('metric_type')
        ->get();
    
    foreach ($metricTypes as $type) {
        echo "  - {$type->metric_type}: {$type->count} records\n";
    }
    
    echo "\n✅ Test data inserted successfully!\n\n";
    
    echo "Test API endpoints:\n";
    echo "  - /api/monitor-graph/uptime?monitor_id=1&period=24h\n";
    echo "  - /api/monitor-graph/response-time?monitor_id=1&period=24h\n";
    echo "  - /api/monitor-graph/system-metrics?metric_type=cpu_usage&period=24h\n";
    echo "  - /api/monitor-graph/metric-types\n\n";
    
    echo "View demo: /monitor-demo.html\n";
    
    echo "</pre>\n";
    
} catch (\Exception $e) {
    echo "<div style='color: red; padding: 20px; background: #fee;'>\n";
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo $e->getTraceAsString();
    echo "</div>\n";
}
