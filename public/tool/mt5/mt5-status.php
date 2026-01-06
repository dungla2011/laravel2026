<?php
/**
 * Webhook Receiver for MT5 Bot
 * Receives POST data from MQL5 bot every 10 seconds
 */

// Base log directory
define('BASE_LOG_DIR', '/var/glx/weblog/mt5_log');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set response headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

try {
    // Get raw POST data
    $rawData = file_get_contents('php://input');

    if (empty($rawData)) {
        throw new Exception('No data received');
    }

    // Decode JSON data
    $data = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    // ==================================================================
    // PROCESS THE DATA HERE
    // ==================================================================

    // Bot information
    $version = $data['version'] ?? 'unknown';
    $symbol = $data['symbol'] ?? 'unknown';
    $accountNumber = $data['account'] ?? ($data['accountNumber'] ?? 0);

    // Validate account number - reject if 0 or invalid
    if ($accountNumber == 0 || !is_numeric($accountNumber)) {
        throw new Exception('Invalid account number: ' . $accountNumber . '. Please check MT5 bot connection.');
    }

    $accountType = $data['accountType'] ?? 'Unknown';
    $balance = $data['balance'] ?? 0;
    $equity = $data['equity'] ?? 0;

    // Settings
    $settings = $data['settings'] ?? [];
    $lotL = $settings['L'] ?? 0;
    $lotS = $settings['S'] ?? 0;
    $tp = $settings['TP'] ?? 0;
    $sl = $settings['SL'] ?? 0;
    $maxBuy = $settings['MaxB'] ?? 0;
    $direction = $settings['direction'] ?? 'BOTH';
    $tpslMode = $settings['tpslMode'] ?? 'Bot';
    $status = $settings['status'] ?? 'STOP';

    // Orders
    $openOrders = $data['openOrders'] ?? [];
    $closedOrders = $data['closedOrders'] ?? [];

    $config_input = $data['config_input'] ?? '';

    // Profit data
    $profit = $data['profit'] ?? [];
    $totalProfit = $profit['total'] ?? 0;
    $openProfit = $profit['open'] ?? 0;
    $closedProfit = $profit['closed'] ?? 0;
    $todayProfit = $profit['today'] ?? 0;

    // Current price
    $price = $data['price'] ?? [];
    $bid = $price['bid'] ?? 0;
    $ask = $price['ask'] ?? 0;

    // Latest order
    $latestOrder = $data['latestOrder'] ?? null;

    // Recent closed orders
    $recentClosedOrders = $data['recentClosedOrders'] ?? [];

    // Timestamp
    $timestamp = $data['timestamp'] ?? '';

    // ==================================================================
    // CREATE ACCOUNT-SPECIFIC LOG DIRECTORY
    // ==================================================================

    $accountLogDir = BASE_LOG_DIR . '/' . $accountNumber;

    // Create directory if it doesn't exist
    if (!is_dir($accountLogDir)) {
        if (!mkdir($accountLogDir, 0755, true)) {
            throw new Exception('Failed to create log directory: ' . $accountLogDir);
        }
    }

    // Set error log for this account
    ini_set('error_log', $accountLogDir . '/errors.log');

    // ==================================================================
    // SAVE DATA TO ACCOUNT-SPECIFIC FILES
    // ==================================================================

    // 1. Save full webhook data log
    $webhookLogFile = $accountLogDir . '/webhook_data.log';
    $logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($data, JSON_PRETTY_PRINT) . "\n" . str_repeat('=', 80) . "\n";
    file_put_contents($webhookLogFile, $logEntry, FILE_APPEND);

    // 2. Save current status snapshot (overwrite each time)
    $statusFile = $accountLogDir . '/current_status.json';
    file_put_contents($statusFile, json_encode([
        'last_update' => date('Y-m-d H:i:s'),
        'version' => $version,
        'config_input' => $config_input,
        'symbol' => $symbol,
        'account' => $accountNumber,
        'accountType' => $accountType,
        'balance' => $balance,
        'equity' => $equity,
        'status' => $status,
        'direction' => $direction,
        'tpsl_mode' => $tpslMode,
        'settings' => $settings,
        'profit' => $profit,
        'price' => $price,
        'open_orders_count' => count($openOrders),
        'timestamp' => $timestamp
    ], JSON_PRETTY_PRINT));

    // 3. Save open orders (overwrite each time)
    $openOrdersFile = $accountLogDir . '/open_orders.json';
    file_put_contents($openOrdersFile, json_encode($openOrders, JSON_PRETTY_PRINT));

    // 4. Save recent closed orders (overwrite each time)
    $closedOrdersFile = $accountLogDir . '/recent_closed_orders.json';
    file_put_contents($closedOrdersFile, json_encode($recentClosedOrders, JSON_PRETTY_PRINT));

    // 5. Save daily profit history (append)
    $dailyProfitFile = $accountLogDir . '/daily_profit_history.log';
    $profitLogEntry = sprintf(
        "%s | Balance: %.2f | Equity: %.2f | Today: %.2f | Total: %.2f | Open: %.2f | Closed: %.2f\n",
        date('Y-m-d H:i:s'),
        $balance,
        $equity,
        $todayProfit,
        $totalProfit,
        $openProfit,
        $closedProfit
    );
    file_put_contents($dailyProfitFile, $profitLogEntry, FILE_APPEND);

    // 6. Save settings history (log when settings change)
    $settingsFile = $accountLogDir . '/settings_history.log';
    $currentSettings = json_encode($settings);
    $lastSettingsFile = $accountLogDir . '/last_settings.tmp';

    if (!file_exists($lastSettingsFile) || file_get_contents($lastSettingsFile) !== $currentSettings) {
        $settingsLogEntry = sprintf(
            "%s | L:%.2f S:%.2f TP:%.1f SL:%.1f MaxB:%d Dir:%s Mode:%s Status:%s\n",
            date('Y-m-d H:i:s'),
            $lotL, $lotS, $tp, $sl, $maxBuy, $direction, $tpslMode, $status
        );
        file_put_contents($settingsFile, $settingsLogEntry, FILE_APPEND);
        file_put_contents($lastSettingsFile, $currentSettings);
    }

    // 7. Create daily summary file
    $today = date('Y-m-d');
    $dailySummaryFile = $accountLogDir . '/summary_' . $today . '.json';
    $dailySummary = [
        'date' => $today,
        'last_update' => date('Y-m-d H:i:s'),
        'account' => $accountNumber,
        'symbol' => $symbol,
        'balance' => $balance,
        'equity' => $equity,
        'today_profit' => $todayProfit,
        'total_profit' => $totalProfit,
        'status' => $status,
        'total_updates' => 1
    ];

    // Update count if file exists
    if (file_exists($dailySummaryFile)) {
        $existing = json_decode(file_get_contents($dailySummaryFile), true);
        $dailySummary['total_updates'] = ($existing['total_updates'] ?? 0) + 1;
    }

    file_put_contents($dailySummaryFile, json_encode($dailySummary, JSON_PRETTY_PRINT));

    // ==================================================================
    // SAVE TO DATABASE (Example - uncomment and configure as needed)
    // ==================================================================

    /*
    // Database connection
    $db = new PDO('mysql:host=localhost;dbname=mt5_bot', 'username', 'password');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert bot status
    $stmt = $db->prepare("
        INSERT INTO bot_status
        (version, symbol, account_number, balance, equity,
         lot_l, lot_s, tp, sl, max_buy, direction, tpsl_mode, status,
         total_profit, open_profit, closed_profit, today_profit,
         bid, ask, timestamp, created_at)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $version, $symbol, $accountNumber, $balance, $equity,
        $lotL, $lotS, $tp, $sl, $maxBuy, $direction, $tpslMode, $status,
        $totalProfit, $openProfit, $closedProfit, $todayProfit,
        $bid, $ask, $timestamp
    ]);

    // Insert open orders
    if (!empty($openOrders)) {
        $stmt = $db->prepare("
            INSERT INTO open_orders
            (ticket, type, price, volume, current_profit, account_number, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        foreach ($openOrders as $order) {
            $stmt->execute([
                $order['ticket'],
                $order['type'],
                $order['price'],
                $order['volume'],
                $order['currentProfit'],
                $accountNumber
            ]);
        }
    }

    // Insert recent closed orders
    if (!empty($recentClosedOrders)) {
        $stmt = $db->prepare("
            INSERT INTO closed_orders
            (ticket, type, price, volume, profit, close_time, account_number, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            profit = VALUES(profit),
            close_time = VALUES(close_time)
        ");

        foreach ($recentClosedOrders as $order) {
            $stmt->execute([
                $order['ticket'],
                $order['type'],
                $order['price'],
                $order['volume'],
                $order['profit'],
                $order['closeTime'],
                $accountNumber
            ]);
        }
    }
    */

    // ==================================================================
    // SEND NOTIFICATION (Example - Telegram)
    // ==================================================================

    /*
    // Send alert if bot status changes or significant profit/loss
    if ($status === 'STOP') {
        sendTelegramMessage("⚠️ Bot STOPPED - {$symbol} - Account: {$accountNumber}");
    }

    if ($todayProfit > 100) {
        sendTelegramMessage("✅ Today Profit: +\${$todayProfit} - {$symbol}");
    }

    if ($todayProfit < -100) {
        sendTelegramMessage("🔴 Today Loss: \${$todayProfit} - {$symbol}");
    }

    function sendTelegramMessage($message) {
        $botToken = 'YOUR_TELEGRAM_BOT_TOKEN';
        $chatId = 'YOUR_CHAT_ID';

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $postData = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
    */

    // ==================================================================
    // DISPLAY DATA (for debugging - remove in production)
    // ==================================================================

    echo json_encode([
        'status' => 'success',
        'message' => 'Data received successfully',
        'received_at' => date('Y-m-d H:i:s'),
        'summary' => [
            'version' => $version,
            'symbol' => $symbol,
            'account' => $accountNumber,
            'balance' => $balance,
            'equity' => $equity,
            'status' => $status,
            'open_orders_count' => count($openOrders),
            'recent_closed_count' => count($recentClosedOrders),
            'today_profit' => $todayProfit
        ],
        'data' => $data // Include full data for debugging
    ], JSON_PRETTY_PRINT);

    http_response_code(200);

} catch (Exception $e) {
    // Log error
    error_log('Webhook Error: ' . $e->getMessage());

    // Return error response
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
