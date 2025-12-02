<?php

/**
 * Service Manager Package - Example Usage
 * 
 * File này demo cách sử dụng các tính năng chính của package
 */

use YourCompany\ServiceManager\Models\ServicePlan;
use YourCompany\ServiceManager\Models\Service;
use YourCompany\ServiceManager\Models\UserBalance;
use YourCompany\ServiceManager\Services\ServiceProvisioningService;
use YourCompany\ServiceManager\Services\BillingService;
use YourCompany\ServiceManager\Services\ResourceCalculatorService;

// 1. Tạo Service Plan
echo "=== Tạo Service Plan ===\n";

$plan = ServicePlan::create([
    'name' => 'VPS Standard',
    'description' => 'Gói VPS tiêu chuẩn với cấu hình linh hoạt',
    'category' => 'vps',
    'status' => true,
    'resources' => [
        'cpu' => 2,      // 2 CPU cores
        'ram' => 4,      // 4GB RAM
        'disk' => 50,    // 50GB disk
        'network' => 100, // 100Mbps
        'ip' => 1        // 1 IP address
    ],
    'pricing' => [
        'cpu' => [
            'minute' => 0.1,
            'hour' => 5,
            'day' => 100,
            'month' => 2000
        ],
        'ram' => [
            'minute' => 0.05,
            'hour' => 2.5,
            'day' => 50,
            'month' => 1000
        ],
        'disk' => [
            'minute' => 0.01,
            'hour' => 0.5,
            'day' => 10,
            'month' => 200
        ],
        'network' => [
            'minute' => 0.02,
            'hour' => 1,
            'day' => 20,
            'month' => 400
        ],
        'ip' => [
            'hour' => 10,
            'day' => 200,
            'month' => 5000
        ]
    ],
    'metadata' => [
        'max_resources' => [
            'cpu' => 16,
            'ram' => 64,
            'disk' => 1000,
            'network' => 1000,
            'ip' => 10
        ],
        'min_resources' => [
            'cpu' => 1,
            'ram' => 1,
            'disk' => 10,
            'network' => 10,
            'ip' => 1
        ]
    ],
    'created_by' => 1
]);

echo "✅ Đã tạo plan: {$plan->name} (ID: {$plan->_id})\n";

// 2. Nạp tiền cho user
echo "\n=== Nạp tiền cho user ===\n";

$userId = 1;
$balance = UserBalance::getOrCreateForUser($userId);
$transaction = $balance->addFunds(5000000, 'Nạp tiền ban đầu'); // 5 triệu VND

echo "✅ Đã nạp 5,000,000 VND. Số dư hiện tại: " . number_format($balance->balance) . " VND\n";

// 3. Tạo dịch vụ cho khách hàng
echo "\n=== Tạo dịch vụ ===\n";

$provisioningService = app(ServiceProvisioningService::class);

$service = $provisioningService->createService(
    $userId,
    $plan->_id,
    [
        'cpu' => 4,      // Tăng lên 4 CPU
        'ram' => 8,      // Tăng lên 8GB RAM
        'disk' => 100,   // Tăng lên 100GB
        'network' => 200, // Tăng lên 200Mbps
        'ip' => 2        // 2 IP addresses
    ],
    'month',
    [
        'name' => 'Production Server',
        'description' => 'Server chính cho production'
    ]
);

echo "✅ Đã tạo dịch vụ: {$service->name} (ID: {$service->_id})\n";
echo "💰 Chi phí hàng tháng: " . number_format($service->calculateCurrentCost()) . " VND\n";

// 4. Kiểm tra số dư sau khi tạo dịch vụ
$balance = $balance->fresh();
echo "💳 Số dư còn lại: " . number_format($balance->balance) . " VND\n";

// 5. Tính toán chi phí khi thay đổi tài nguyên
echo "\n=== Tính toán thay đổi tài nguyên ===\n";

$billingService = app(BillingService::class);

$newResources = [
    'cpu' => 6,      // Tăng lên 6 CPU
    'ram' => 16,     // Tăng lên 16GB RAM
    'disk' => 200,   // Tăng lên 200GB
    'network' => 500, // Tăng lên 500Mbps
    'ip' => 3        // 3 IP addresses
];

$billing = $billingService->calculateProratedBilling($service, $newResources);

echo "📊 Chi phí thay đổi tài nguyên:\n";
echo "   - Chi phí chênh lệch: " . number_format($billing['cost_difference']) . " VND/tháng\n";
echo "   - Chi phí prorated: " . number_format($billing['amount']) . " VND\n";
echo "   - Thời gian còn lại: {$billing['remaining_minutes']} phút\n";
echo "   - Tỷ lệ prorated: " . round(($billing['prorated_ratio'] ?? 1) * 100, 2) . "%\n";

// 6. Áp dụng thay đổi tài nguyên
echo "\n=== Áp dụng thay đổi tài nguyên ===\n";

if ($balance->hasSufficientBalance($billing['amount'])) {
    $result = $billingService->processResourceChangeBilling($service, $newResources);
    
    echo "✅ Đã thay đổi tài nguyên thành công\n";
    echo "💰 Đã trừ: " . number_format($result['amount']) . " VND\n";
    
    // Refresh service và balance
    $service = $service->fresh();
    $balance = $balance->fresh();
    
    echo "🖥️  Tài nguyên mới:\n";
    foreach ($service->current_resources as $type => $value) {
        echo "   - {$type}: {$value}\n";
    }
    echo "💳 Số dư còn lại: " . number_format($balance->balance) . " VND\n";
} else {
    echo "❌ Không đủ số dư để thay đổi tài nguyên\n";
}

// 7. Lấy lịch sử sử dụng tài nguyên
echo "\n=== Lịch sử thay đổi tài nguyên ===\n";

$resourceHistory = $service->resourceUsageHistory()->get();
foreach ($resourceHistory as $usage) {
    echo "📅 {$usage->change_date->format('Y-m-d H:i:s')}\n";
    echo "   Chi phí thay đổi: " . number_format($usage->cost_difference) . " VND\n";
    
    $changes = $usage->getResourceChangesSummary();
    foreach ($changes as $type => $change) {
        echo "   {$type}: {$change['old']} → {$change['new']} ({$change['change_type']})\n";
    }
    echo "\n";
}

// 8. Lấy thống kê dịch vụ của user
echo "\n=== Thống kê dịch vụ ===\n";

$summary = $provisioningService->getUserServicesSummary($userId);

echo "📊 Tổng quan:\n";
echo "   - Tổng số dịch vụ: {$summary['total_services']}\n";
echo "   - Dịch vụ đang hoạt động: {$summary['active_services']}\n";
echo "   - Dịch vụ bị tạm dừng: {$summary['suspended_services']}\n";
echo "   - Dịch vụ đã hủy: {$summary['terminated_services']}\n";
echo "   - Tổng chi phí hàng tháng: " . number_format($summary['total_monthly_cost']) . " VND\n";

// 9. Lấy lịch sử giao dịch
echo "\n=== Lịch sử giao dịch ===\n";

$transactions = $balance->transactions()->orderBy('created_at', 'desc')->limit(5)->get();
foreach ($transactions as $transaction) {
    $type = $transaction->type === 'credit' ? '💰 Nạp tiền' : '💸 Trừ tiền';
    echo "{$type}: " . number_format($transaction->amount) . " VND - {$transaction->description}\n";
    echo "   Thời gian: {$transaction->created_at->format('Y-m-d H:i:s')}\n";
    echo "   Số dư: " . number_format($transaction->balance_before) . " → " . number_format($transaction->balance_after) . " VND\n\n";
}

// 10. Demo tính năng khuyến nghị tài nguyên
echo "\n=== Khuyến nghị tài nguyên ===\n";

$resourceCalculator = app(ResourceCalculatorService::class);

// Giả lập metrics sử dụng
$usageMetrics = [
    'cpu_usage' => 85,    // 85% CPU usage
    'ram_usage' => 90,    // 90% RAM usage
    'disk_usage' => 60    // 60% disk usage
];

$recommendations = $resourceCalculator->getResourceRecommendations($service, $usageMetrics);

if (!empty($recommendations)) {
    echo "💡 Khuyến nghị nâng cấp:\n";
    foreach ($recommendations as $type => $rec) {
        echo "   {$type}: {$rec['current']} → {$rec['recommended']} ({$rec['priority']} priority)\n";
        echo "      Lý do: {$rec['reason']}\n";
    }
    
    // Tính chi phí nếu áp dụng khuyến nghị
    $costImpact = $resourceCalculator->calculateRecommendationCost($service, $recommendations);
    if ($costImpact) {
        echo "\n💰 Chi phí nếu áp dụng khuyến nghị:\n";
        echo "   Chi phí hiện tại: " . number_format($costImpact['old_cost']) . " VND/tháng\n";
        echo "   Chi phí mới: " . number_format($costImpact['new_cost']) . " VND/tháng\n";
        echo "   Chênh lệch: " . number_format($costImpact['difference']) . " VND/tháng\n";
        echo "   Tăng: " . round($costImpact['percentage_change'], 2) . "%\n";
    }
} else {
    echo "✅ Tài nguyên hiện tại đã phù hợp\n";
}

echo "\n🎉 Demo hoàn thành!\n"; 