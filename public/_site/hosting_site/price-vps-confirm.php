<?php

use App\Models\VpsInstance;
use App\Models\VpsPlan;
use App\Models\Product_Meta;

// Only handle POST/AJAX requests
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

if (!($isAjax || $isPost)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}


require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


// Get user ID
$uid = getCurrentUserId();

if (!$uid) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'User not authenticated']));
}

// Anti-spam: Check if user already created instance in last 30 seconds
$recentInstance = VpsInstance::where('user_id', $uid)
    ->where('created_at', '>', now()->subSeconds(60))
    ->first();

if ($recentInstance) {
    http_response_code(429);
    die(json_encode([
        'success' => false,
        'error_code' => 'RATE_LIMIT_60',
        'message' => '⏱️ Tạo instance quá nhanh. Vui lòng chờ 60 giây trước khi tạo instance tiếp theo.',
        'retry_after' => 60
    ]));
}

// Anti-spam: Check daily limit (max 10 instances per day)
$dailyCount = VpsInstance::where('user_id', $uid)
    ->where('created_at', '>', now()->startOfDay())
    ->count();

if ($dailyCount >= 10) {
    http_response_code(429);
    die(json_encode([
        'success' => false,
        'error_code' => 'DAILY_LIMIT_EXCEEDED',
        'message' => '📅 Bạn đã tạo 10 instances hôm nay. Giới hạn hàng ngày đã đạt. Vui lòng thử lại vào ngày mai.',
        'daily_limit' => 10,
        'created_today' => $dailyCount
    ]));
}

// Get request data
$postData = $_REQUEST;

// Extract VPS specs
$nCpuCore = isset($postData['n_cpu_core']) ? intval($postData['n_cpu_core']) : 1;
$nRamGb = isset($postData['n_ram_gb']) ? intval($postData['n_ram_gb']) : 1;
$nGbDisk = isset($postData['n_gb_disk']) ? intval($postData['n_gb_disk']) : 20;
$nNetworkMbit = isset($postData['n_network_mbit']) ? intval($postData['n_network_mbit']) : 200;
$nNetworkDedicatedMbit = isset($postData['n_network_dedicated_mbit']) ? intval($postData['n_network_dedicated_mbit']) : 0;
$nIpAddress = isset($postData['n_ip_address']) ? intval($postData['n_ip_address']) : 1;
$planId = isset($postData['plan_id']) ? intval($postData['plan_id']) : null;

// Calculate price
$price = Product_Meta::calculateVpsPrice(
    $nCpuCore,
    $nRamGb,
    $nGbDisk,
    $nNetworkMbit,
    $nNetworkDedicatedMbit,
    $nIpAddress
);

// Load pricing config to calculate breakdown
$vpsConfig = include('/var/www/html/config/vps_config.php');
$vpsConfigSpecs = $vpsConfig['specs'];

// Get specs and prices
$cpuSpec = $vpsConfigSpecs['n_cpu_core'] ?? [];
$ramSpec = $vpsConfigSpecs['n_ram_gb'] ?? [];
$diskSpec = $vpsConfigSpecs['n_gb_disk'] ?? [];
$networkSpec = $vpsConfigSpecs['n_network_dedicated_mbit'] ?? [];
$ipSpec = $vpsConfigSpecs['n_ip_address'] ?? [];

// Get free quantities
$freeCPU = $cpuSpec['free'] ?? 0;
$freeRAM = $ramSpec['free'] ?? 0;
$freeDisk = $diskSpec['free'] ?? 0;
$freeNetwork = $networkSpec['free'] ?? 0;
$freeIps = $ipSpec['free'] ?? 0;

// Get rounding and prices
$diskRounding = $diskSpec['rounding'] ?? 10;
$networkRounding = $networkSpec['rounding'] ?? 100;

$cpuPrice = $cpuSpec['price'] ?? 50;
$ramPrice = $ramSpec['price'] ?? 30;
$diskPrice = $diskSpec['price'] ?? 1;
$networkPrice = $networkSpec['price'] ?? 1000;
$ipPrice = $ipSpec['price'] ?? 50;

// Calculate breakdown in K (thousands), applying free quantities
$chargedCPU = max(0, $nCpuCore - $freeCPU);
$cpuPriceK = $chargedCPU * $cpuPrice;

$chargedRAM = max(0, $nRamGb - $freeRAM);
$ramPriceK = $chargedRAM * $ramPrice;

$diskRounded = ceil($nGbDisk / $diskRounding) * $diskRounding;
$chargedDisk = max(0, $diskRounded - $freeDisk);
$diskPriceK = $chargedDisk * $diskPrice;

$networkPriceK = 0;
$networkRounded = 0;
if ($nNetworkDedicatedMbit > $freeNetwork) {
    $networkRounded = ceil($nNetworkDedicatedMbit / $networkRounding) * $networkRounding;
    $chargedNetwork = $networkRounded - $freeNetwork;
    $networkPriceK = ($chargedNetwork / 100) * $networkPrice;
}

$extraIps = max(0, $nIpAddress - $freeIps);
$ipPriceK = $extraIps * $ipPrice;

// Build breakdown array
$breakdown = [
    'cpu' => [
        'quantity' => $chargedCPU,
        'unit_price' => $cpuPrice * 1000,
        'total' => $cpuPriceK * 1000,
        'detail' => $chargedCPU . ' Core @ ' . number_format($cpuPrice * 1000, 0, ',', '.') . 'đ/Core (' . $freeCPU . ' free)'
    ],
    'ram' => [
        'quantity' => $chargedRAM,
        'unit_price' => $ramPrice * 1000,
        'total' => $ramPriceK * 1000,
        'detail' => $chargedRAM . ' GB @ ' . number_format($ramPrice * 1000, 0, ',', '.') . 'đ/GB (' . $freeRAM . ' free)'
    ],
    'disk' => [
        'quantity' => $chargedDisk,
        'unit_price' => $diskPrice * 1000,
        'total' => $diskPriceK * 1000,
        'detail' => $chargedDisk . ' GB (rounded from ' . $nGbDisk . ' GB) @ ' . number_format($diskPrice * 1000, 0, ',', '.') . 'đ/GB (' . $freeDisk . ' free)'
    ]
];

if ($nNetworkDedicatedMbit > 0) {
    $breakdown['network_dedicated'] = [
        'bandwidth' => $networkRounded - $freeNetwork,
        'detail' => ($networkRounded - $freeNetwork) . ' Mbps @ ' . number_format($networkPrice * 1000, 0, ',', '.') . 'đ/100Mbps (' . $freeNetwork . ' free)',
        'total' => $networkPriceK * 1000
    ];
}

if ($extraIps > 0) {
    $breakdown['ip_address'] = [
        'quantity' => $extraIps,
        'detail' => $extraIps . ' extra IP' . ($extraIps > 1 ? 's' : '') . ' @ ' . number_format($ipPrice * 1000, 0, ',', '.') . 'đ/IP (' . $freeIps . ' free)',
        'total' => $ipPriceK * 1000
    ];
} else {
    $breakdown['ip_address'] = [
        'quantity' => $nIpAddress,
        'detail' => $nIpAddress . ' IP (' . $freeIps . ' free)',
        'total' => 0
    ];
}

// Prepare comprehensive infos JSON with full breakdown
$infos = [
    'post' => 'vps',
    'configuration' => [
        'n_cpu_core' => $nCpuCore,
        'n_ram_gb' => $nRamGb,
        'n_gb_disk' => $nGbDisk,
        'n_network_mbit' => $nNetworkMbit,
        'n_network_dedicated_mbit' => $nNetworkDedicatedMbit,
        'n_ip_address' => $nIpAddress,

    ],
    'pricing' => [
        'price_1core_cpu' => $cpuPrice,
        'price_1gb_ram' => $ramPrice,
        'price_1gb_disk' => $diskPrice,
        'price_100mbit_network' => $networkPrice,
        'price_1_ip' => $ipPrice
    ],
    'breakdown' => $breakdown,
    'total_price' => $price,
    'total_price_formatted' => number_format($price, 0, ',', '.') . 'đ',
    'created_at' => now()->toDateTimeString()
];

try {
    // Validate required fields
    if ($nCpuCore < 1 || $nRamGb < 1 || $nGbDisk < 1 || $nIpAddress < 1) {
        throw new \Exception('Invalid VPS specifications', 1001); // INVALID_SPECS
    }

    if ($price < 0) {
        throw new \Exception('Invalid calculated price', 1002); // INVALID_PRICE
    }

    // Security: Validate specs are within reasonable limits
    if ($nCpuCore > 128 || $nRamGb > 512 || $nGbDisk > 5000 || $nNetworkDedicatedMbit > 100000) {
        throw new \Exception('Specifications exceed maximum allowed limits', 1003); // SPECS_TOO_HIGH
    }

    // Security: Prevent duplicate creation with same specs in short timeframe
    $duplicateCheck = VpsInstance::where('user_id', $uid)
        ->where('cpu', $nCpuCore)
        ->where('ram_gb', $nRamGb)
        ->where('disk_gb', $nGbDisk)
        ->where('network_mbit', $nNetworkDedicatedMbit)
        ->where('number_ip_address', $nIpAddress)
        ->where('created_at', '>', now()->subMinutes(5))
        ->first();

    if ($duplicateCheck) {
        throw new \Exception('Duplicate instance with same specifications created within 5 minutes. Please wait or modify specifications.', 1004); // DUPLICATE_INSTANCE
    }

    // Get plan info if plan_id provided
    $plan = null;
    if ($planId) {
        $plan = VpsPlan::findOrFail($planId);
    }

    // Generate instance name
    $instanceName = 'vps-' . $uid . '-' . time();

    // Calculate price per minute from monthly price
    $pricePerMinute = $price / (30 * 24 * 60);

    // Create VpsInstance record with transaction to ensure data consistency
    $vpsInstance = VpsInstance::create([
        'name' => $instanceName,
        'user_id' => $uid,
        'plan_id' => $planId,
        'cpu' => $nCpuCore,
        'ram_gb' => $nRamGb,
        'disk_gb' => $nGbDisk,
        'network_mbit' => $nNetworkDedicatedMbit,
        'number_ip_address' => $nIpAddress,
        'price_per_minute' => $pricePerMinute,
        'power_state' => 'powered_off',
        'infos' => json_encode($infos)
    ]);

    if (!$vpsInstance || !$vpsInstance->id) {
        throw new \Exception('Failed to create VpsInstance record', 1005); // CREATION_FAILED
    }

    // Success response - JSON only
    http_response_code(200);
    die(json_encode([
        'success' => true,
        'message' => 'VPS instance created successfully',
        'instance_id' => $vpsInstance->id,
        'data' => [
            'user_id' => $uid,
            'instance_id' => $vpsInstance->id,
            'instance_name' => $instanceName,
            'price' => $price,
            'price_formatted' => number_format($price, 0, ',', '.') . 'đ',
            'price_per_minute' => $pricePerMinute,
            'infos' => $infos
        ]
    ]));

} catch (\Illuminate\Database\QueryException $e) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'error_code' => 'DB_ERROR',
        'message' => 'Database error',
        'error_detail' => $e->getMessage()
    ]));

} catch (\Exception $e) {
    // Error code mapping
    $errorCodeMap = [
        1001 => ['code' => 'INVALID_SPECS', 'message' => '❌ Cấu hình VPS không hợp lệ. Tất cả thông số phải lớn hơn 0.', 'http_code' => 400],
        1002 => ['code' => 'INVALID_PRICE', 'message' => '💰 Lỗi tính giá. Vui lòng kiểm tra lại cấu hình.', 'http_code' => 400],
        1003 => ['code' => 'SPECS_TOO_HIGH', 'message' => '⚡ Cấu hình vượt quá giới hạn cho phép. CPU max 128 cores, RAM max 512 GB, Disk max 5000 GB, Network max 100 Gbps.', 'http_code' => 400],
        1004 => ['code' => 'DUPLICATE_INSTANCE', 'message' => '⚠️ Bạn đã tạo instance với cùng cấu hình trong 5 phút. Vui lòng chờ hoặc thay đổi cấu hình.', 'http_code' => 429],
        1005 => ['code' => 'CREATION_FAILED', 'message' => '🔴 Lỗi tạo instance. Vui lòng thử lại.', 'http_code' => 500],
    ];
    
    $errorCode = $e->getCode();
    $errorInfo = $errorCodeMap[$errorCode] ?? [
        'code' => 'UNKNOWN_ERROR',
        'message' => '❓ Lỗi không xác định. Vui lòng liên hệ hỗ trợ.',
        'http_code' => 500
    ];
    
    // Log error with code for easier tracking
    \Illuminate\Support\Facades\Log::warning('VPS Instance error: ' . $errorInfo['code'], [
        'error_code' => $errorCode,
        'user_id' => $uid,
        'error_message' => $e->getMessage(),
        'specs' => [
            'cpu' => $nCpuCore ?? null,
            'ram' => $nRamGb ?? null,
            'disk' => $nGbDisk ?? null,
            'network' => $nNetworkDedicatedMbit ?? null,
            'ip' => $nIpAddress ?? null
        ],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'timestamp' => now(),
        'stack_trace' => $e->getTraceAsString()
    ]);

    http_response_code($errorInfo['http_code']);
    die(json_encode([
        'success' => false,
        'error_code' => $errorInfo['code'],
        'message' => $errorInfo['message'],
        'error_detail' => $e->getMessage()
    ]));
}
