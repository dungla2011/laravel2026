<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['vps_plans', 'vps_instances', 'vps_instance_config_history', 'vps_usage'];

foreach ($tables as $table) {
    echo "\n========== $table ==========\n";
    
    if (Schema::hasTable($table)) {
        echo "[OK] Table EXISTS\n\n";
        
        // Get columns
        $columns = DB::select("DESCRIBE $table");
        echo "Columns:\n";
        foreach ($columns as $col) {
            $type = $col->Type;
            $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
            $default = $col->Default ? "DEFAULT {$col->Default}" : '';
            echo "  - {$col->Field} ({$type}) {$null} {$default}\n";
        }
        
        // Count rows
        $count = DB::table($table)->count();
        echo "\nRows: $count\n";
        
    } else {
        echo "[ERROR] Table NOT FOUND\n";
    }
}

echo "\n========== Summary ==========\n";
echo "Run migration: php artisan migrate --path=config/sql_vps_table\n";
