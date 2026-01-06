<?php
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║          CẤU TRÚC BẢNG vps_usages                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Lấy thông tin columns từ bảng vps_usages
$columns = Schema::getColumns('vps_usages');

echo sprintf("%-4s | %-25s | %-20s | %-10s | %-20s\n", "#", "Column Name", "Type", "Nullable", "Default");
echo str_repeat("-", 100) . "\n";

$i = 1;
foreach ($columns as $column) {
    printf(
        "%-4d | %-25s | %-20s | %-10s | %-20s\n",
        $i++,
        $column['name'],
        $column['type'],
        $column['nullable'] ? 'Yes' : 'No',
        $column['default'] ?? '-'
    );
}

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      INDEXES                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$indexes = DB::select("SHOW INDEXES FROM vps_usages");
$indexGroups = [];
foreach ($indexes as $index) {
    $indexGroups[$index->Key_name][] = $index->Column_name;
}

foreach ($indexGroups as $indexName => $columns) {
    echo "📌 $indexName: " . implode(", ", $columns) . "\n";
}

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                    STATISTICS                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$count = DB::table('vps_usages')->count();
echo "📊 Total records: $count\n";

$stats = DB::table('vps_usages')
    ->selectRaw('
        COUNT(*) as total,
        COUNT(DISTINCT user_id) as users,
        COUNT(DISTINCT instance_id) as instances,
        COUNT(DISTINCT power_state) as power_states,
        MIN(created_at) as oldest,
        MAX(created_at) as newest,
        AVG(CAST(price_per_minute AS DECIMAL(10,8))) as avg_price
    ')
    ->first();

echo "👥 Unique Users: " . $stats->users . "\n";
echo "🖥️  Unique Instances: " . $stats->instances . "\n";
echo "⚡ Power States: " . $stats->power_states . "\n";
echo "📅 Date Range: {$stats->oldest} to {$stats->newest}\n";
echo "💰 Avg Price/Minute: " . number_format($stats->avg_price, 8) . "\n";

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                 SAMPLE DATA (First 5)                          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$samples = DB::table('vps_usages')
    ->selectRaw('
        id, 
        name, 
        instance_id, 
        user_id, 
        price_per_minute, 
        power_state, 
        cpu,
        ram_gb,
        disk_gb,
        timestamp_minute,
        created_at
    ')
    ->limit(5)
    ->orderByDesc('id')
    ->get();

foreach ($samples as $idx => $row) {
    echo sprintf(
        "[%d] ID: %d | Instance: %d | User: %d\n",
        $idx + 1,
        $row->id,
        $row->instance_id,
        $row->user_id
    );
    echo sprintf(
        "    Name: %s | Power: %s\n",
        $row->name,
        $row->power_state
    );
    echo sprintf(
        "    Hardware: CPU=%d, RAM=%dGB, Disk=%dGB\n",
        $row->cpu,
        $row->ram_gb,
        $row->disk_gb
    );
    echo sprintf(
        "    Price/Min: %.8f | Timestamp: %s\n",
        $row->price_per_minute,
        $row->timestamp_minute
    );
    echo sprintf(
        "    Created: %s\n\n",
        $row->created_at
    );
}

echo "✅ Done!\n";
