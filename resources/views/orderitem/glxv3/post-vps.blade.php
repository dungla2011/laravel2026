<?php

use App\Models\OrderInfo;
use App\Models\Product_Meta;

// Get user ID
$uid = getCurrentUserId();

if (!$uid) {
    die('User not authenticated');
}

// Get POST/GET data
$postData = $_REQUEST;
$post = isset($postData['post']) ? $postData['post'] : null;

if ($post !== 'vps') {
    die('Invalid request');
}

// Extract VPS specs
$nCpuCore = isset($postData['n_cpu_core']) ? intval($postData['n_cpu_core']) : 1;
$nRamGb = isset($postData['n_ram_gb']) ? intval($postData['n_ram_gb']) : 1;
$nGbDisk = isset($postData['n_gb_disk']) ? intval($postData['n_gb_disk']) : 20;
$nNetworkMbit = isset($postData['n_network_mbit']) ? intval($postData['n_network_mbit']) : 200;
$nNetworkDedicatedMbit = isset($postData['n_network_dedicated_mbit']) ? intval($postData['n_network_dedicated_mbit']) : 0;
$nIpAddress = isset($postData['n_ip_address']) ? intval($postData['n_ip_address']) : 0;
$productId = isset($postData['product_id']) ? intval($postData['product_id']) : 5;

// Load config
$vpsConfig = config('vps_config');
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

// Calculate prices for each component (in K - thousands), applying free quantities
$chargedCPU = max(0, $nCpuCore - $freeCPU);
$cpuPriceTotal = $chargedCPU * $cpuPrice;

$chargedRAM = max(0, $nRamGb - $freeRAM);
$ramPriceTotal = $chargedRAM * $ramPrice;

// Disk: round up by step, then apply free quantity
$diskRounded = ceil($nGbDisk / $diskRounding) * $diskRounding;
$chargedDisk = max(0, $diskRounded - $freeDisk);
$diskPriceTotal = $chargedDisk * $diskPrice;

// Dedicated Network: round up by step, then apply free quantity
$networkPriceTotal = 0;
$networkRounded = 0;
if ($nNetworkDedicatedMbit > $freeNetwork) {
    $networkRounded = ceil($nNetworkDedicatedMbit / $networkRounding) * $networkRounding;
    $chargedNetwork = $networkRounded - $freeNetwork;
    $networkPriceTotal = ($chargedNetwork / 100) * $networkPrice;
}

// IP Address: charge only above free count
$ipPriceTotal = 0;
$extraIps = max(0, $nIpAddress - $freeIps);
if ($extraIps > 0) {
    $ipPriceTotal = $extraIps * $ipPrice;
}

// Total price (in K)
$totalPriceK = $cpuPriceTotal + $ramPriceTotal + $diskPriceTotal + $networkPriceTotal + $ipPriceTotal;
$totalPrice = $totalPriceK * 1000; // Convert to VND

// Format prices
$formatPrice = function($priceK) {
    return number_format($priceK * 1000, 0, ',', '.');
};

$formatPriceK = function($priceK) {
    return number_format($priceK, 0, ',', '.');
};

?>


<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">Chi tiết đơn hàng VPS</h5>

            <!-- Price Breakdown Table -->
            <table class="table table-sm mb-3" style="font-size: 0.9rem;">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th>Mục</th>
                        <th class="text-right">Giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- CPU -->
                    <tr>
                        <td><?php echo $cpuSpec['desc']; ?></td>
                        <td class="text-right"><?php echo $formatPriceK($cpuPrice); ?>đ</td>
                        <td class="text-center"><?php echo $chargedCPU; ?><?php echo ($freeCPU > 0 ? ' (+' . $freeCPU . ' free)' : ''); ?></td>
                        <td class="text-right font-weight-bold"><?php echo $formatPrice($cpuPriceTotal); ?>đ</td>
                    </tr>

                    <!-- RAM -->
                    <tr>
                        <td><?php echo $ramSpec['desc']; ?></td>
                        <td class="text-right"><?php echo $formatPriceK($ramPrice); ?>đ</td>
                        <td class="text-center"><?php echo $chargedRAM; ?><?php echo ($freeRAM > 0 ? ' (+' . $freeRAM . ' free)' : ''); ?></td>
                        <td class="text-right font-weight-bold"><?php echo $formatPrice($ramPriceTotal); ?>đ</td>
                    </tr>

                    <!-- Disk -->
                    <tr>
                        <td><?php echo $diskSpec['desc']; ?></td>
                        <td class="text-right"><?php echo $formatPriceK($diskPrice); ?>đ</td>
                        <td class="text-center"><?php echo $chargedDisk; ?><?php echo ($freeDisk > 0 ? ' (+' . $freeDisk . ' free)' : ''); ?></td>
                        <td class="text-right font-weight-bold"><?php echo $formatPrice($diskPriceTotal); ?>đ</td>
                    </tr>

                    <!-- Dedicated Network -->
                    <?php if ($nNetworkDedicatedMbit > 0): ?>
                    <tr>
                        <td><?php echo $networkSpec['desc']; ?></td>
                        <td class="text-right"><?php echo $formatPriceK($networkPrice); ?>đ/100Mbps</td>
                        <td class="text-center"><?php echo ($networkRounded - $freeNetwork); ?> Mbps<?php echo ($freeNetwork > 0 ? ' (+' . $freeNetwork . ' free)' : ''); ?></td>
                        <td class="text-right font-weight-bold"><?php echo $formatPrice($networkPriceTotal); ?>đ</td>
                    </tr>
                    <?php endif; ?>

                    <!-- IP Address -->
                    <tr>
                        <td><?php echo $ipSpec['desc']; ?></td>
                        <td class="text-right"><?php echo $formatPriceK($ipPrice); ?>đ</td>
                        <td class="text-center"><?php echo $extraIps; ?><?php echo ($freeIps > 0 ? ' (+' . $freeIps . ' free)' : ''); ?></td>
                        <td class="text-right font-weight-bold"><?php echo $formatPrice($ipPriceTotal); ?>đ</td>
                    </tr>

                    <!-- Total Row -->
                    <tr style="background: #e8f4f8; font-weight: bold; border-top: 2px solid #dee2e6;">
                        <td colspan="3">Tổng cộng</td>
                        <td class="text-right" style="color: #dc3545; font-size: 1.1rem;"><?php echo number_format($totalPrice, 0, ',', '.'); ?>đ</td>
                    </tr>
                </tbody>
            </table>

            <div class="text-muted text-right" style="font-size: 0.8rem; margin-bottom: 20px;">/tháng</div>

            <hr class="my-3">

            <div class="d-flex gap-2">
                <button id="cancelBtn" class="btn btn-secondary btn-sm flex-grow-1">Huỷ bỏ</button>
                <button id="confirmBtn" class="btn btn-success btn-sm flex-grow-1">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    // Cancel button - go back
    cancelBtn.addEventListener('click', function() {
        window.history.back();
    });

    // Confirm button - save to database
    confirmBtn.addEventListener('click', function() {
        confirmBtn.disabled = true;
        cancelBtn.disabled = true;
        confirmBtn.innerText = 'Đang xử lý...';

        // Get current URL parameters
        const params = new URLSearchParams(window.location.search);

        // POST to price-vps-confirm.php with AJAX header
        fetch('/_site/hosting_site/price-vps-confirm.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Đơn hàng đã được lưu thành công!\nMã VPS: ' + data.instance_id);
                window.location.href = '/';
            } else {
                alert('❌ Lỗi: ' + data.message);
                confirmBtn.disabled = false;
                cancelBtn.disabled = false;
                confirmBtn.innerText = 'Xác nhận';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Lỗi: ' + error.message);
            confirmBtn.disabled = false;
            cancelBtn.disabled = false;
            confirmBtn.innerText = 'Xác nhận';
        });
    });
</script>
