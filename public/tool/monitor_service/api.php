<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Models\MonitorItem;

//$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'monitor.mytree.vn';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user_id = getCurrentUserId();
if(!$user_id){
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => 'Unauthorized: User not logged in',
        'code' => 401
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Rate limiting - 1 request per 3 seconds per IP per command
function checkRateLimit($cmd = '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $session_dir = sys_get_temp_dir();

    // Include command in the rate limit file name for per-command limiting
    $cmd_suffix = $cmd ? '_' . preg_replace('/[^a-zA-Z0-9]/', '', $cmd) : '';
    $rate_limit_file = $session_dir . '/glx_monitor_api_rate_' . md5($ip) . $cmd_suffix . '.txt';

    $current_time = time();
    $min_interval = 2; // 3 seconds

    // Check if file exists and read last request time
    if (file_exists($rate_limit_file)) {
        $last_request_time = (int)file_get_contents($rate_limit_file);
        $time_diff = $current_time - $last_request_time;

        if ($time_diff < $min_interval) {
            $remaining_time = $min_interval - $time_diff;
            http_response_code(429);
            echo json_encode([
                'status' => 'error',
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => 'Rate limit exceeded for command "' . $cmd . '". Please wait ' . $remaining_time . ' seconds before making another request.',
                'code' => 429,
                'retry_after' => $remaining_time,
                'ip' => $ip,
                'command' => $cmd
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Update last request time
    file_put_contents($rate_limit_file, $current_time);

    // Clean up old rate limit files (optional - run occasionally)
    if (rand(1, 100) === 1) { // 1% chance to cleanup
        cleanupOldRateLimitFiles($session_dir);
    }
}

function cleanupOldRateLimitFiles($dir) {
    $files = glob($dir . '/glx_monitor_api_rate_*.txt');
    $current_time = time();

    foreach ($files as $file) {
        if (file_exists($file)) {
            $file_time = (int)file_get_contents($file);
            // Remove files older than 1 hour
            if ($current_time - $file_time > 3600) {
                unlink($file);
            }
        }
    }
}

// Remove global rate limiting call since we'll do it per command
// checkRateLimit();

class MonitorAPI
{
    public $user_id;
    /**
     * Main router - Parameter-based
     */
    public function handleRequest($user_id)
    {
        $cmd = $_GET['cmd'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'];

        // Apply rate limiting per command
        checkRateLimit($cmd);

        try {
            switch ($cmd) {
                case 'status':
                    $this->getStatus($user_id);
                    break;

                case 'monitors':
                    $this->getMonitors($this->user_id);
                    break;

                case 'logs':
                    $this->getLogs($this->user_id);
                    break;

                case 'statistics':
                    $this->getStatistics($this->user_id);
                    break;

                case 'monitor':
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        $this->sendError('Missing monitor ID', 400);
                        return;
                    }
                    $this->getMonitor($id);
                    break;

//                case 'toggle':
//                    if ($method !== 'POST') {
//                        $this->sendError('POST method required', 405);
//                        return;
//                    }
//                    $id = $_POST['id'] ?? $_GET['id'] ?? null;
//                    if (!$id) {
//                        $this->sendError('Missing monitor ID', 400);
//                        return;
//                    }
//                    $this->toggleMonitor($id);
//                    break;

//                case 'bulk_toggle':
//                    if ($method !== 'POST') {
//                        $this->sendError('POST method required', 405);
//                        return;
//                    }
//                    $this->bulkToggleMonitors();
//                    break;

                case 'need_attention':
                    $this->getNeedAttention();
                    break;

                case 'update_status':
                    if ($method !== 'POST') {
                        $this->sendError('POST method required', 405);
                        return;
                    }
                    $this->updateMonitorStatus();
                    break;

                default:
                    $this->sendError('Invalid command. Available: status, monitors, logs, statistics, monitor, toggle, bulk_toggle, need_attention, update_status', 400);
            }
        } catch (Exception $e) {
            $this->sendError('API Error: ' . $e->getMessage());
        }
    }

    /**
     * Get service status - Sử dụng Laravel Eloquent
     */
    private function getStatus($user_id)
    {
        try {
            // Count total monitors
            $total = MonitorItem::enabled($user_id)->count();

            // Count active threads (monitors checked in last 5 minutes)
            $active = MonitorItem::countActiveThreads(5, $user_id);

            // Get uptime (mock data - could calculate from log files)
            $uptime = $this->calculateUptime();

            $this->sendSuccess([
                'status' => 'running',
                'timestamp' => date('Y-m-d H:i:s'),
                'total_monitors' => $total,
                'active_threads' => $active,
                'uptime' => $uptime,
                'version' => '2025.1.0'
            ]);

        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Get monitors with pagination and filtering - Sử dụng Laravel Eloquent
     */
    private function getMonitors( $user_id = null)
    {



        try {
            $limit = min((int)($_GET['limit'] ?? 50), 1000);
            $offset = max(0, (int)($_GET['offset'] ?? 0));
            $status = $_GET['status'] ?? null;
            $search = $_GET['search'] ?? null;
//            $user_id = $_GET['user_id'] ?? null;

            // Build query với Laravel Eloquent
            $query = MonitorItem::enabled( $user_id);

            // Apply filters
            if ($status) {
                $query = $query->byStatus($status);
            }

            if ($search) {
                $query = $query->search($search);
            }

            if ($user_id) {
                $query = $query->byUser($user_id);
            }

            // Get total count
            $total = $query->count();

            // Get monitors with pagination
            $monitors = $query->orderByDesc('id')
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->map(function($monitor) {
                    return [
                        'id' => $monitor->id,
                        'name' => $monitor->name,
                        'url_check' => $monitor->url_check,
                        'type' => $monitor->type,
//                        'port' => $monitor->port,
                        'last_check_status' => $monitor->status, // Using accessor
                        'last_check_time' => $monitor->last_check_time,
                        'last_check_time_human' => $monitor->last_check_time, // Using accessor
                        'expected_text' => $monitor->expected_text,
                        'user_id' => $monitor->user_id,
                        'enable' => $monitor->enable,
                        'count_online' => $monitor->count_online,
                        'count_offline' => $monitor->count_offline,
//                        'uptime_percentage' => $monitor->uptime_percentage, // Using accessor
                        'stopTo' => $monitor->stopTo,
//                        'response_time' => $monitor->response_time, // Using accessor
//                        'is_healthy' => $monitor->isHealthy() // Using method
                    ];
                })
                ->toArray();

            $this->sendSuccess([
                'monitors' => $monitors,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total
            ]);

        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Get recent logs (from log files or database)
     */
    private function getLogs($user_id)
    {

        $sid = \App\Models\SiteMng::getSiteId();

        $basePathLogs = "/var/glx/monitor/logs";
//        if($sid == 58)
//            $basePathLogs = "/code/logs";

        try {
            $limit = min((int)($_GET['limit'] ?? 50), 100);
            $level = $_GET['level'] ?? null;

            $logs = [];

            // Read from user-specific log files if user_id specified
            if ($user_id) {
                $userLogFile = "$basePathLogs/log_user_{$user_id}.txt";
                if (file_exists($userLogFile)) {

                    $logs = array_merge($logs, $this->parseLogFile($userLogFile, $limit));
                }
                else{
                    die("NOT FOUND LOG FILE $user_id!");
                }
            }
            else{
                // Read from main log file
                $logFile = "$basePathLogs/log_main.txt";
                if (file_exists($logFile)) {
                    $logs = array_merge($logs, $this->parseLogFile($logFile, $limit));
                }
                else
                    die("NOT FOUND LOG FILE!!");
            }

//            echo file_get_contents($userLogFile);

            // Sort by timestamp (newest first)
            usort($logs, function($a, $b) {
//                return strtotime($b['timestamp']) < strtotime($a['timestamp']);
            });

            // Apply limit
            $logs = array_slice($logs, 0, $limit);

            // Filter by level if specified
            if ($level) {
                $logs = array_filter($logs, function($log) use ($level) {
                    return strpos($log['message'], strtoupper($level)) !== false;
                });
                $logs = array_values($logs); // Re-index
            }

            $this->sendSuccess([
                'logs' => $logs,
                'total' => count($logs),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            $this->sendError('Error reading logs: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for dashboard - Sử dụng Eloquent
     */
    private function getStatistics($user_id)
    {
        try {
            $stats = MonitorItem::getStatistics($user_id);
            $this->sendSuccess($stats);

        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Get single monitor details - Sử dụng Laravel Eloquent
     */
    private function getMonitor($id, $user_id = null)
    {
        try {
            $monitor = MonitorItem::enabled($user_id)->find($id);

            if (!$monitor) {
                $this->sendError('Monitor not found', 404);
                return;
            }

            // Format data với đầy đủ thông tin
            $monitorData = [
                'id' => $monitor->id,
                'name' => $monitor->name,
                'url_check' => $monitor->url_check,
                'check_url' => $monitor->getCheckUrl(), // Using method
                'type' => $monitor->type,
                'port' => $monitor->port,
                'last_check_status' => $monitor->status, // Using accessor
                'last_check_time' => $monitor->last_check_time,
                'last_check_time_human' => $monitor->last_check_time_human, // Using accessor
                'expected_text' => $monitor->expected_text,
                'user_id' => $monitor->user_id,
                'enable' => $monitor->enable,
                'count_online' => $monitor->count_online,
                'count_offline' => $monitor->count_offline,
                'uptime_percentage' => $monitor->uptime_percentage, // Using accessor
                'stopTo' => $monitor->stopTo,
                'response_time' => $monitor->response_time, // Using accessor
                'is_healthy' => $monitor->isHealthy(), // Using method
                'created_at' => $monitor->created_at ? $monitor->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $monitor->updated_at ? $monitor->updated_at->format('Y-m-d H:i:s') : null,
            ];

            $this->sendSuccess(['monitor' => $monitorData]);

        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Toggle monitor enable/disable - Sử dụng Laravel Eloquent
     */
    private function toggleMonitor($id)
    {
        try {
            $monitor = MonitorItem::find($id);

            if (!$monitor) {
                $this->sendError('Monitor not found', 404);
                return;
            }

            // Toggle status using model method
            $monitor->toggle();

            $this->sendSuccess([
                'monitor_id' => $monitor->id,
                'enabled' => $monitor->enable,
                'message' => $monitor->enable ? 'Monitor enabled' : 'Monitor disabled',
                'status' => $monitor->status, // Current status
                'uptime_percentage' => $monitor->uptime_percentage
            ]);

        } catch (Exception $e) {
            $this->sendError('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Parse log file
     */
    private function parseLogFile($filename, $limit = 50)
    {
        $logs = [];
//
//        die("111 $filename");
        if (!file_exists($filename)) {
            return $logs;
        }
        $sizeLog = 2048;

        //Chỉ lấy 1kb cuối của file:
        $filesize = filesize($filename);
        $offset = $filesize > $sizeLog ? $filesize - $sizeLog : 0;
        $handle = fopen($filename, 'r');
        if ($handle) {
            fseek($handle, $offset);
            $content = fread($handle, $sizeLog);
            fclose($handle);
            $lines = explode("\n", $content);
        } else {
            return $logs;
        }

        // Hoặc dùng file() để đọc toàn bộ file nếu file nhỏ
        if ($filesize <= $sizeLog) {
            $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } else {
            $lines = (array_slice($lines, -$limit)); // Get last N lines
            //Bỏ đi dòng đầu
//            array_shift($lines);
        }

        foreach ($lines as $line) {
            if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})#(.+)$/', $line, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'message' => trim($matches[2]),
                    'level' => $this->detectLogLevel($matches[2])
                ];
            }
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($logs);
//        echo "</pre>";
//        die();

        return $logs;
    }

    /**
     * Helper: Detect log level from message
     */
    private function detectLogLevel($message)
    {
        if (strpos($message, '❌') !== false || strpos($message, 'ERROR') !== false) {
            return 'error';
        } elseif (strpos($message, '⚠️') !== false || strpos($message, 'WARNING') !== false) {
            return 'warning';
        } elseif (strpos($message, '✅') !== false || strpos($message, 'SUCCESS') !== false) {
            return 'success';
        } else {
            return 'info';
        }
    }

    /**
     * Helper: Get mock response time
     */
    private function getResponseTime($monitorId)
    {
        // This could be stored in a separate table or calculated from logs
        return round(mt_rand(50, 500) + (mt_rand(0, 100) / 100), 2);
    }

    /**
     * Helper: Calculate uptime
     */
    private function calculateUptime()
    {
        // Mock uptime - could be calculated from service start time
        $hours = mt_rand(1, 72);
        $minutes = mt_rand(0, 59);
        return "{$hours}h {$minutes}m";
    }

    /**
     * Send success response
     */
    private function sendSuccess($data)
    {
        echo json_encode([
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $data
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Send error response
     */
    private function sendError($message, $code = 500)
    {
        http_response_code($code);
        echo json_encode([
            'status' => 'error',
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => $message,
            'code' => $code
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Bulk toggle monitors
     */
    private function bulkToggleMonitors()
    {
        try {
            $ids = $_POST['ids'] ?? [];
            $enable = $_POST['enable'] ?? null; // true, false, hoặc null (toggle)

            if (empty($ids) || !is_array($ids)) {
                $this->sendError('Missing or invalid monitor IDs array', 400);
                return;
            }

            // Convert enable to boolean if provided
            if ($enable !== null) {
                $enable = filter_var($enable, FILTER_VALIDATE_BOOLEAN);
            }

            $result = MonitorItem::bulkToggle($ids, $enable);

            $this->sendSuccess([
                'affected_monitors' => is_array($result) ? count($result) : $result,
                'action' => $enable === null ? 'toggled' : ($enable ? 'enabled' : 'disabled'),
                'monitor_ids' => $ids
            ]);

        } catch (Exception $e) {
            $this->sendError('Bulk toggle error: ' . $e->getMessage());
        }
    }

    /**
     * Get monitors that need attention
     */
    private function getNeedAttention()
    {
        try {
            $limit = min((int)($_GET['limit'] ?? 20), 50);

            $monitors = MonitorItem::getNeedAttention($limit);

            $data = $monitors->map(function($monitor) {
                return [
                    'id' => $monitor->id,
                    'name' => $monitor->name,
                    'url_check' => $monitor->url_check,
                    'status' => $monitor->status,
                    'last_check_time' => $monitor->last_check_time,
                    'last_check_time_human' => $monitor->last_check_time_human,
                    'uptime_percentage' => $monitor->uptime_percentage,
                    'is_healthy' => $monitor->isHealthy(),
                    'type' => $monitor->type,
                    'user_id' => $monitor->user_id
                ];
            })->toArray();

            $this->sendSuccess([
                'monitors_need_attention' => $data,
                'total' => count($data),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            $this->sendError('Error getting monitors: ' . $e->getMessage());
        }
    }

    /**
     * Update monitor check status
     */
    private function updateMonitorStatus()
    {
        try {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null; // 1 (online) or 0 (offline)
            $responseTime = $_POST['response_time'] ?? null;

            if (!$id || $status === null) {
                $this->sendError('Missing required parameters: id, status', 400);
                return;
            }

            $monitor = MonitorItem::find($id);
            if (!$monitor) {
                $this->sendError('Monitor not found', 404);
                return;
            }

            // Update check status
            $monitor->updateCheckStatus($status, $responseTime);

            $this->sendSuccess([
                'monitor_id' => $monitor->id,
                'status' => $monitor->status,
                'last_check_time' => $monitor->last_check_time->format('Y-m-d H:i:s'),
                'count_online' => $monitor->count_online,
                'count_offline' => $monitor->count_offline,
                'uptime_percentage' => $monitor->uptime_percentage,
                'message' => 'Monitor status updated successfully'
            ]);

        } catch (Exception $e) {
            $this->sendError('Update status error: ' . $e->getMessage());
        }
    }
}

// Initialize and handle request
try {
    $api = new MonitorAPI();
    $api->user_id = getCurrentUserId();;
    $api->handleRequest($api->user_id );
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => 'Internal server error: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
