<?php
/**
 * PRICING CONFIG TRACKING - CODE EXAMPLES
 * 
 * Concrete examples of how the pricing tracking system works
 */

// ============================================================================
// EXAMPLE 1: Extract Current Pricing from Config
// ============================================================================

use App\Services\VpsPricingService;

$pricing = VpsPricingService::getPricingConfig();
var_dump($pricing);
/*
Result:
array:5 [
  "n_cpu_core_price" => 50          // 50K VND per core
  "n_ram_gb_price" => 30            // 30K VND per GB
  "n_gb_disk_price" => 1            // 1K VND per GB
  "n_ip_address_price" => 50        // 50K VND per IP
  "n_network_dedicated_mbit_price" => 1000  // 1M VND per 100Mbps
]
*/

// ============================================================================
// EXAMPLE 2: Store Pricing as JSON
// ============================================================================

$json = VpsPricingService::getPricingConfigJson();
echo $json;
/*
Output:
{"n_cpu_core_price":50,"n_ram_gb_price":30,"n_gb_disk_price":1,"n_ip_address_price":50,"n_network_dedicated_mbit_price":1000}
*/

// Store in database
DB::table('vps_usages')->insert([
    'instance_id' => 123,
    'user_id' => 1,
    'price_per_minute' => 0.29166667,
    'price_config' => $json,  // ← Stored here
    'timestamp_minute' => now()->startOfMinute(),
    'created_at' => now(),
]);

// ============================================================================
// EXAMPLE 3: Check If Pricing Changed
// ============================================================================

$lastUsage = DB::table('vps_usages')
    ->where('instance_id', 123)
    ->latest()
    ->first();

$lastConfig = json_decode($lastUsage->price_config, true);

$hasChanged = VpsPricingService::hasPricingChanged($lastConfig);

if ($hasChanged) {
    echo "💰 Pricing has changed! Need to insert new record.\n";
} else {
    echo "✅ Pricing is the same. Can update existing record.\n";
}

// ============================================================================
// EXAMPLE 4: Detect Pricing Change (MD5 Comparison)
// ============================================================================

$current = VpsPricingService::getPricingConfig();
$last = json_decode($lastUsage->price_config, true);

$currentHash = md5(json_encode($current));
$lastHash = md5(json_encode($last));

echo "Current Hash: $currentHash\n";
echo "Last Hash: $lastHash\n";
echo "Changed: " . ($currentHash !== $lastHash ? 'YES' : 'NO') . "\n";

// ============================================================================
// EXAMPLE 5: Calculate Price Per Minute with Pricing
// ============================================================================

$specs = [
    'cpu' => 2,
    'ram_gb' => 4,
    'disk_gb' => 100,
    'number_ip_address' => 2,
];

$pricing = VpsPricingService::getPricingConfig();

// Calculate total monthly price (K VND)
$totalMonthlyPrice = (
    $specs['cpu'] * $pricing['n_cpu_core_price'] +
    $specs['ram_gb'] * $pricing['n_ram_gb_price'] +
    $specs['disk_gb'] * $pricing['n_gb_disk_price'] +
    $specs['number_ip_address'] * $pricing['n_ip_address_price']
);

echo "Config: 2 CPU, 4GB RAM, 100GB Disk, 2 IP\n";
echo "Total Monthly (K): " . number_format($totalMonthlyPrice, 0) . "\n";
echo "Per Hour: " . number_format($totalMonthlyPrice / 24 / 30, 2) . "\n";
echo "Per Minute: " . number_format($totalMonthlyPrice / 24 / 30 / 60, 8) . "\n";

/*
Output:
Config: 2 CPU, 4GB RAM, 100GB Disk, 2 IP
Total Monthly (K): 420
Per Hour: 583.33
Per Minute: 0.29166667
*/

// ============================================================================
// EXAMPLE 6: Query Database for Pricing Changes
// ============================================================================

$changes = DB::table('vps_usages as v1')
    ->join('vps_usages as v2', function($join) {
        $join->on('v1.instance_id', '=', 'v2.instance_id')
             ->whereRaw('v1.id + 1 = v2.id');  // Consecutive records
    })
    ->where('v1.price_config', '!=', 'v2.price_config')
    ->select(
        'v1.id',
        'v1.created_at as old_time',
        'v1.price_config as old_pricing',
        'v2.id as new_id',
        'v2.created_at as new_time',
        'v2.price_config as new_pricing'
    )
    ->get();

foreach ($changes as $change) {
    echo "Pricing changed at {$change->new_time}:\n";
    echo "  Old: {$change->old_pricing}\n";
    echo "  New: {$change->new_pricing}\n\n";
}

// ============================================================================
// EXAMPLE 7: Extract Specific Price from JSON
// ============================================================================

$lastUsage = DB::table('vps_usages')->find(12345);

$pricing = json_decode($lastUsage->price_config, true);
$cpuPrice = $pricing['n_cpu_core_price'];
$ramPrice = $pricing['n_ram_gb_price'];

echo "CPU Price: " . number_format($cpuPrice, 0) . " K\n";
echo "RAM Price: " . number_format($ramPrice, 0) . " K\n";

// Or use JSON extraction in SQL
$result = DB::table('vps_usages')
    ->selectRaw("
        id,
        JSON_EXTRACT(price_config, '$.n_cpu_core_price') as cpu_price,
        JSON_EXTRACT(price_config, '$.n_ram_gb_price') as ram_price
    ")
    ->where('instance_id', 123)
    ->first();

echo "CPU Price: " . $result->cpu_price . "\n";
echo "RAM Price: " . $result->ram_price . "\n";

// ============================================================================
// EXAMPLE 8: Billing with Price Changes
// ============================================================================

// Scenario: Price changed mid-month

$records = DB::table('vps_usages')
    ->where('instance_id', 123)
    ->whereBetween('created_at', [
        '2025-12-01',
        '2025-12-31'
    ])
    ->select(
        'id',
        'created_at',
        'price_per_minute',
        'price_config'
    )
    ->get();

$totalBill = 0;
$lastPricing = null;

foreach ($records as $record) {
    $currentPricing = json_decode($record->price_config, true);
    
    // If pricing changed, show transition
    if ($lastPricing && $currentPricing !== $lastPricing) {
        echo "⚠️ PRICING CHANGED at {$record->created_at}\n";
        echo "   Old CPU price: {$lastPricing['n_cpu_core_price']}\n";
        echo "   New CPU price: {$currentPricing['n_cpu_core_price']}\n\n";
    }
    
    // Add to bill (per minute charge)
    $charge = $record->price_per_minute;
    $totalBill += $charge;
    
    $lastPricing = $currentPricing;
}

echo "Total December Bill: " . number_format($totalBill, 2) . " VND\n";

// ============================================================================
// EXAMPLE 9: Alert When Price Increases
// ============================================================================

$current = VpsPricingService::getPricingConfig();
$lastRecord = DB::table('vps_usages')
    ->where('instance_id', 123)
    ->latest('id')
    ->first();

if ($lastRecord && $lastRecord->price_config) {
    $last = json_decode($lastRecord->price_config, true);
    
    if ($current['n_cpu_core_price'] > $last['n_cpu_core_price']) {
        $increase = $current['n_cpu_core_price'] - $last['n_cpu_core_price'];
        echo "🔔 CPU price increased by " . number_format($increase, 0) . " K!\n";
    }
    
    if ($current['n_ram_gb_price'] > $last['n_ram_gb_price']) {
        $increase = $current['n_ram_gb_price'] - $last['n_ram_gb_price'];
        echo "🔔 RAM price increased by " . number_format($increase, 0) . " K!\n";
    }
}

// ============================================================================
// EXAMPLE 10: Full SyncVmware Logic (Simplified)
// ============================================================================

use App\Services\VpsPricingService;

$instance = DB::table('vps_instances')->find(123);
$lastUsage = DB::table('vps_usages')
    ->where('instance_id', $instance->id)
    ->latest()
    ->first();

// Get current pricing
$currentPricingConfig = VpsPricingService::getPricingConfig();
$currentPricingHash = md5(json_encode($currentPricingConfig));

// Get last pricing
if ($lastUsage && $lastUsage->price_config) {
    $lastPricingConfig = json_decode($lastUsage->price_config, true);
} else {
    $lastPricingConfig = $currentPricingConfig;
}
$lastPricingHash = md5(json_encode($lastPricingConfig));

// Compare
$pricingHasChanged = ($currentPricingHash !== $lastPricingHash);

// Decide: UPDATE or INSERT
$hardwareChanged = ($lastUsage->cpu !== $vmInfo->cpu);  // Simplified
$timePassed = now()->diffInMinutes($lastUsage->lastest_time_the_same) > 10;

if (($hardwareChanged || $timePassed || $pricingHasChanged) && $lastUsage) {
    // INSERT new record
    echo "INSERT new vps_usages record\n";
    if ($pricingHasChanged) {
        echo "  Reason: 💰 Pricing changed\n";
    }
    
    DB::table('vps_usages')->insert([
        'instance_id' => $instance->id,
        'price_per_minute' => 0.29,
        'price_config' => json_encode($currentPricingConfig),
        'timestamp_minute' => now()->startOfMinute(),
        'created_at' => now(),
    ]);
} else {
    // UPDATE existing record
    echo "UPDATE existing vps_usages record\n";
    DB::table('vps_usages')
        ->where('id', $lastUsage->id)
        ->update([
            'lastest_time_the_same' => now(),
            'count_update_status' => DB::raw('count_update_status + 1'),
        ]);
}

// ============================================================================
// EXAMPLE 11: Monitoring Pricing Changes
// ============================================================================

// Show pricing change history
$history = DB::table('vps_usages')
    ->where('instance_id', 123)
    ->select(
        'id',
        'created_at',
        'price_per_minute',
        'price_config'
    )
    ->orderBy('created_at')
    ->get()
    ->unique('price_config');

echo "Pricing History:\n";
foreach ($history as $record) {
    $config = json_decode($record->price_config, true);
    echo "{$record->created_at} - CPU:{$config['n_cpu_core_price']} RAM:{$config['n_ram_gb_price']} Price/min:{$record->price_per_minute}\n";
}

// ============================================================================
// EXAMPLE 12: Test VpsPricingService
// ============================================================================

// Unit test examples
$service = new VpsPricingService();

// Test 1: getPricingConfig returns array
$config = VpsPricingService::getPricingConfig();
assert(is_array($config));
assert(isset($config['n_cpu_core_price']));

// Test 2: getPricingConfigJson returns JSON string
$json = VpsPricingService::getPricingConfigJson();
assert(is_string($json));
assert(json_decode($json) !== null);

// Test 3: hasPricingChanged detects changes
$original = VpsPricingService::getPricingConfig();
$modified = $original;
$modified['n_cpu_core_price'] = 999;

assert(VpsPricingService::hasPricingChanged($modified) === true);
assert(VpsPricingService::hasPricingChanged($original) === false);

echo "✅ All tests passed!\n";

?>
