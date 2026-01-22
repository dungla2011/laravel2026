<?php
/**
 * MT5 Bot Monitoring API
 * Returns aggregated data from all account folders
 */

//Set time zone asia /ho_chi_minh
//date_default_timezone_set('Asia/Ho_Chi_Minh');

// Base log directory
define('BASE_LOG_DIR', '/var/glx/weblog/mt5_log');

// Set response headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $accounts = [];

    // Check if base directory exists
    if (!is_dir(BASE_LOG_DIR)) {
        throw new Exception('Log directory not found: ' . BASE_LOG_DIR);
    }

    // Scan all account folders
    $folders = scandir(BASE_LOG_DIR);

    foreach ($folders as $folder) {
        // Skip . and ..
        if ($folder === '.' || $folder === '..') {
            continue;
        }

        $accountPath = BASE_LOG_DIR . '/' . $folder;

        // Only process directories with numeric names (account numbers)
        if (!is_dir($accountPath) || !is_numeric($folder)) {
            continue;
        }

        $accountNumber = $folder;
        $accountData = [
            'account' => $accountNumber,
            'status' => null,
            'openOrders' => [],
            'recentClosedOrders' => [],
            'lastUpdate' => null,
            'files' => []
        ];

        // Read current_status.json
        $statusFile = $accountPath . '/current_status.json';
        if (file_exists($statusFile)) {
            $status = json_decode(file_get_contents($statusFile), true);
            $accountData['status'] = $status;
            $accountData['lastUpdate'] = $status['last_update'] ?? null;
        }

        // Read open_orders.json
        $openOrdersFile = $accountPath . '/open_orders.json';
        if (file_exists($openOrdersFile)) {
            $openOrders = json_decode(file_get_contents($openOrdersFile), true);
            $accountData['openOrders'] = $openOrders; // Keep entire object with count and orders
        }

        // Read recent_closed_orders.json
        $closedOrdersFile = $accountPath . '/recent_closed_orders.json';
        if (file_exists($closedOrdersFile)) {
            $closedOrders = json_decode(file_get_contents($closedOrdersFile), true);
            $accountData['recentClosedOrders'] = $closedOrders; // Keep entire object
        }

        // Read today's summary
        $today = date('Y-m-d');
        $summaryFile = $accountPath . '/summary_' . $today . '.json';
        if (file_exists($summaryFile)) {
            $summary = json_decode(file_get_contents($summaryFile), true);
            $accountData['todaySummary'] = $summary;
        }

        // Get file modification times
        $accountData['files'] = [
            'status' => file_exists($statusFile) ? filemtime($statusFile) : null,
            'openOrders' => file_exists($openOrdersFile) ? filemtime($openOrdersFile) : null,
            'closedOrders' => file_exists($closedOrdersFile) ? filemtime($closedOrdersFile) : null,
        ];

        // Calculate time since last update
        if ($accountData['lastUpdate']) {
            $lastUpdateTime = strtotime($accountData['lastUpdate']);
            $accountData['secondsSinceUpdate'] = time() - $lastUpdateTime;
            $accountData['isOnline'] = ($accountData['secondsSinceUpdate'] < 30); // Online if updated within 30 seconds
        }

        $accounts[] = $accountData;
    }

    // Sort accounts by account number
    usort($accounts, function($a, $b) {
        return $a['account'] <=> $b['account'];
    });

    // Calculate summary for Real accounts
    $realAccountsCount = 0;
    $totalRealProfit = 0;
    $totalOpenProfit = 0;

    foreach ($accounts as $account) {
        $status = $account['status'] ?? [];
        $accountType = $status['accountType'] ?? 'Unknown';
        if ($accountType === 'Real') {
            $realAccountsCount++;
            $profit = $status['profit'] ?? [];
            $todayProfit = $profit['todayProfit'] ?? 0;
            $openProfit = $profit['open'] ?? 0;
            $totalRealProfit += $todayProfit;
            $totalOpenProfit += $openProfit;
        }
    }

    // Return response
    echo json_encode([
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'serverTime' => time(),
        'accountCount' => count($accounts),
        'realAccountsCount' => $realAccountsCount,
        'totalRealProfit' => round($totalRealProfit, 2),
        'totalOpenProfit' => round($totalOpenProfit, 2),
        'accounts' => $accounts
    ], JSON_PRETTY_PRINT);

    http_response_code(200);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
