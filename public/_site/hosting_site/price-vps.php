<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'ncbd.mytree.vn';
$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.lad.vn';
//require_once '/var/www/html/public/index.php';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// API tính giá VPS
// Endpoint: /public/_site/hosting_site/price-vps.php?n_cpu_core=2&n_ram_gb=4&n_gb_disk=50&n_network_mbit=300
// Hoặc POST với JSON

header('Content-Type: application/json; charset=utf-8');

try {
    // Lấy parameters từ GET hoặc POST (sử dụng tên attributes từ database)
    $nCore = isset($_REQUEST['n_cpu_core']) ? intval($_REQUEST['n_cpu_core']) : 1;
    $nGBRam = isset($_REQUEST['n_ram_gb']) ? intval($_REQUEST['n_ram_gb']) : 1;
    $nGBDisk = isset($_REQUEST['n_gb_disk']) ? intval($_REQUEST['n_gb_disk']) : 10;
    $nNetworkMbit = isset($_REQUEST['n_network_mbit']) ? intval($_REQUEST['n_network_mbit']) : 200;
    $nNetworkDedicatedMbit = isset($_REQUEST['n_network_dedicated_mbit']) ? intval($_REQUEST['n_network_dedicated_mbit']) : 0;
    $nIPAddress = isset($_REQUEST['n_ip_address']) ? intval($_REQUEST['n_ip_address']) : 1;
    $priceElementId = isset($_REQUEST['price_element_id']) ? intval($_REQUEST['price_element_id']) : 8;

    // Validate parameters
    if ($nCore < 1 || $nGBRam < 1 || $nGBDisk < 1) {
        throw new Exception('Invalid parameters: n_core, n_gb_ram, n_gb_disk must be >= 1');
    }

    // Gọi hàm calculateVpsPrice từ Product_Meta (trả về VND)
    $totalPrice = \App\Models\Product_Meta::calculateVpsPrice($nCore, $nGBRam, $nGBDisk, $nNetworkMbit, $nNetworkDedicatedMbit, $nIPAddress, $priceElementId);

    // Lấy config specs
    $vpsConfigSpecs = config('vps_config.specs');
    
    // Get specs
    $cpuSpec = $vpsConfigSpecs['n_cpu_core'] ?? [];
    $ramSpec = $vpsConfigSpecs['n_ram_gb'] ?? [];
    $diskSpec = $vpsConfigSpecs['n_gb_disk'] ?? [];
    $networkSpec = $vpsConfigSpecs['n_network_dedicated_mbit'] ?? [];
    $ipSpec = $vpsConfigSpecs['n_ip_address'] ?? [];
    
    // Get rounding and prices
    $diskRounding = $diskSpec['rounding'] ?? 10;
    $networkRounding = $networkSpec['rounding'] ?? 100;
    
    $cpuPrice = $cpuSpec['price'] ?? 50;
    $ramPrice = $ramSpec['price'] ?? 30;
    $diskPrice = $diskSpec['price'] ?? 1;
    $networkPrice = $networkSpec['price'] ?? 1000;
    $ipPrice = $ipSpec['price'] ?? 50;
    
    $freeCPU = $cpuSpec['free'] ?? 0;
    $freeRAM = $ramSpec['free'] ?? 0;
    $freeDisk = $diskSpec['free'] ?? 0;
    $freeNetwork = $networkSpec['free'] ?? 0;
    $freeIP = $ipSpec['free'] ?? 0;

    // Áp dụng rounding (giống như calculateVpsPrice)
    $nGBDiskRounded = ceil($nGBDisk / $diskRounding) * $diskRounding;
    $nNetworkDedicatedMbitRounded = ceil($nNetworkDedicatedMbit / $networkRounding) * $networkRounding;

    // Convert K to VND
    $price_1core_cpu = $cpuPrice * 1000;
    $price_1gb_ram = $ramPrice * 1000;
    $price_1gb_disk = $diskPrice * 1000;
    $price_100mbit_network = $networkPrice * 1000;
    $price_1_ip = $ipPrice * 1000;

    // Tính chi tiết từng phần (áp dụng free quantity)
    $chargedCPU = max(0, $nCore - $freeCPU);
    $priceFromCPU = $chargedCPU * $price_1core_cpu;
    
    $chargedRAM = max(0, $nGBRam - $freeRAM);
    $priceFromRam = $chargedRAM * $price_1gb_ram;
    
    $chargedDisk = max(0, $nGBDiskRounded - $freeDisk);
    $priceFromDisk = $chargedDisk * $price_1gb_disk;

    // Tính giá Network Dedicated (áp dụng free network)
    $priceFromNetworkDedicated = 0;
    $networkDetail = 'No dedicated';
    if ($nNetworkDedicatedMbitRounded > $freeNetwork) {
        $chargedNetwork = $nNetworkDedicatedMbitRounded - $freeNetwork;
        $dedicatedBandwidth = ($chargedNetwork / 100);
        $priceFromNetworkDedicated = $dedicatedBandwidth * $price_100mbit_network;
        $networkDetail = $chargedNetwork . ' Mbps @ ' . number_format($price_100mbit_network, 0, ',', '.') . 'đ/100Mbps';
    }

    // Tính giá IP (áp dụng free IP)
    $priceFromIP = 0;
    $ipDetail = 'Free (' . $freeIP . ' included)';
    if ($nIPAddress > $freeIP) {
        $chargedIPs = $nIPAddress - $freeIP;
        $priceFromIP = $chargedIPs * $price_1_ip;
        $ipDetail = $chargedIPs . ' extra IPs @ ' . number_format($price_1_ip, 0, ',', '.') . 'đ/IP';
    }

    // Verify total matches calculateVpsPrice return
    $calcTotal = $priceFromCPU + $priceFromRam + $priceFromDisk + $priceFromNetworkDedicated + $priceFromIP;

    // Trả về JSON response
    $response = [
        'success' => true,
        'data' => [
            'configuration' => [
                'n_cpu_core' => $nCore,
                'n_ram_gb' => $nGBRam,
                'n_gb_disk' => $nGBDiskRounded,
                'n_network_mbit' => $nNetworkMbit,
                'n_network_dedicated_mbit' => $nNetworkDedicatedMbitRounded,
                'n_ip_address' => $nIPAddress
            ],
            'pricing' => [
                'price_1core_cpu' => $price_1core_cpu,
                'price_1gb_ram' => $price_1gb_ram,
                'price_1gb_disk' => $price_1gb_disk,
                'price_100mbit_network' => $price_100mbit_network,
                'price_1_ip' => $price_1_ip
            ],
            'breakdown' => [
                'cpu' => [
                    'quantity' => $nCore,
                    'unit_price' => $price_1core_cpu,
                    'total' => $priceFromCPU
                ],
                'ram' => [
                    'quantity' => $nGBRam,
                    'unit_price' => $price_1gb_ram,
                    'total' => $priceFromRam
                ],
                'disk' => [
                    'quantity' => $nGBDiskRounded,
                    'unit_price' => $price_1gb_disk,
                    'total' => $priceFromDisk
                ],
                'network_dedicated' => [
                    'bandwidth' => $nNetworkDedicatedMbitRounded,
                    'detail' => $networkDetail,
                    'total' => $priceFromNetworkDedicated
                ],
                'ip_address' => [
                    'quantity' => $nIPAddress,
                    'detail' => $ipDetail,
                    'total' => $priceFromIP
                ]
            ],
            'total_price' => $totalPrice,
            'total_price_formatted' => number_format($totalPrice, 0, ',', '.') . 'đ'
        ]
    ];

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
