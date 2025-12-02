<?php

/**
 * Script migrate sản phẩm VPS sang bảng vps_plans
 * Lấy sản phẩm có type='vps_glx' và các attribute liên quan
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\VpsPlan;
use Illuminate\Database\Capsule\Manager as DB;

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get all VPS products
$vpsProducts = Product::where('type', 'vps_glx')->get();

if ($vpsProducts->count() === 0) {
    echo "❌ Không tìm thấy sản phẩm VPS nào (type='vps_glx')\n";
    exit(1);
}

echo "✓ Tìm thấy " . $vpsProducts->count() . " sản phẩm VPS\n\n";

foreach ($vpsProducts as $product) {
    echo "Processing: {$product->name} (ID: {$product->id})\n";
    
    // Get all attributes for this product
    $attributes = ProductAttribute::where('product_id', $product->id)->get();
    
    // Build config array
    $config = [];
    foreach ($attributes as $attr) {
        $config[$attr->attribute_name] = $attr->attribute_value;
    }
    
    // Extract values with defaults
    $cpu = (int)($config['n_cpu_core'] ?? 1);
    $ram_gb = (int)($config['n_ram_gb'] ?? 1);
    $disk_gb = (int)($config['n_gb_disk'] ?? 20);
    $network_mbit = (int)($config['n_network_dedicated_mbit'] ?? 0);
    $number_ip_address = (int)($config['n_ip_address'] ?? 1);
    
    // Get price from product (assuming it has a price field)
    $price = (float)($product->price ?? 100);
    $price_per_minute = $price / (30 * 24 * 60); // Chia thành giá/phút (giả sử giá là per tháng)
    
    echo "  CPU: $cpu cores\n";
    echo "  RAM: {$ram_gb}GB\n";
    echo "  Disk: {$disk_gb}GB\n";
    echo "  Network: {$network_mbit}Mbps\n";
    echo "  IP Addresses: {$number_ip_address}\n";
    echo "  Price/month: " . number_format($price, 2) . "đ\n";
    echo "  Price/minute: " . number_format($price_per_minute, 8) . "\n";
    
    // Insert into vps_plans
    try {
        $existing = VpsPlan::where('name', $product->name)->first();
        
        if ($existing) {
            echo "  ⚠️  Plan '{$product->name}' đã tồn tại (ID: {$existing->id}), bỏ qua\n\n";
            continue;
        }
        
        VpsPlan::create([
            'name' => $product->name,
            'status' => $product->status ?? 1,
            'user_id' => $product->user_id ?? null,
            'cpu' => $cpu,
            'ram_gb' => $ram_gb,
            'disk_gb' => $disk_gb,
            'network_mbit' => $network_mbit,
            'number_ip_address' => $number_ip_address,
            'price_per_minute' => $price_per_minute,
        ]);
        
        echo "  ✅ Inserted vào vps_plans\n\n";
        
    } catch (\Exception $e) {
        echo "  ❌ Lỗi: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ Hoàn tất migration!\n";
echo "Chạy lệnh: php artisan migrate --path=config/sql_vps_table\n";
echo "Sau đó kiểm tra: SELECT * FROM vps_plans;\n";
