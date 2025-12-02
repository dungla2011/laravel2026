<?php
/**
 * Check detailed information about sequence status
 */

$GLOBALS['DISABLE_DEBUG_BAR'] = 1;
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "/var/www/html/public/index.php";

echo "<h2>Sequence Status Analysis</h2>\n";
echo "<pre>\n";

$tables = \Illuminate\Support\Facades\DB::select("
    SELECT 
        t.table_name,
        c.column_name,
        c.column_default,
        t.table_schema
    FROM information_schema.tables t
    JOIN information_schema.columns c 
        ON t.table_name = c.table_name 
        AND t.table_schema = c.table_schema
    WHERE t.table_schema NOT IN ('pg_catalog', 'information_schema')
        AND t.table_type = 'BASE TABLE'
        AND c.column_default LIKE 'nextval%'
    ORDER BY t.table_name
");

$needsFix = [];
$isOk = [];

foreach ($tables as $table) {
    try {
        $fullTableName = $table->table_schema . '.' . $table->table_name;
        
        $seqResult = \Illuminate\Support\Facades\DB::select("
            SELECT pg_get_serial_sequence(?, ?) as sequence_name
        ", [$fullTableName, $table->column_name]);
        
        $sequenceName = $seqResult[0]->sequence_name ?? null;
        
        if (empty($sequenceName)) continue;
        
        $currentSeq = \Illuminate\Support\Facades\DB::select("
            SELECT last_value FROM {$sequenceName}
        ");
        
        $maxId = \Illuminate\Support\Facades\DB::select("
            SELECT MAX({$table->column_name}) as max_id, COUNT(*) as total 
            FROM {$fullTableName}
        ");
        
        $currentValue = $currentSeq[0]->last_value ?? 0;
        $maxIdValue = $maxId[0]->max_id ?? 0;
        $totalRecords = $maxId[0]->total ?? 0;
        
        $diff = $maxIdValue - $currentValue;
        
        if ($maxIdValue > $currentValue) {
            $needsFix[] = [
                'table' => $table->table_name,
                'records' => $totalRecords,
                'max_id' => $maxIdValue,
                'sequence' => $currentValue,
                'diff' => $diff
            ];
        } else {
            $isOk[] = [
                'table' => $table->table_name,
                'records' => $totalRecords,
                'max_id' => $maxIdValue,
                'sequence' => $currentValue
            ];
        }
        
    } catch (\Exception $e) {
        // Skip error tables
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TABLES NEED FIXING (" . count($needsFix) . "):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Sort by diff (biggest problem first)
usort($needsFix, function($a, $b) {
    return $b['diff'] - $a['diff'];
});

printf("%-40s %10s %10s %10s %10s\n", "TABLE", "RECORDS", "MAX_ID", "SEQUENCE", "DIFF");
echo str_repeat("-", 90) . "\n";

foreach ($needsFix as $info) {
    printf(
        "%-40s %10d %10d %10d %10d ⚠️\n",
        $info['table'],
        $info['records'],
        $info['max_id'],
        $info['sequence'],
        $info['diff']
    );
}

echo "\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TABLES OK (" . count($isOk) . "):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

printf("%-40s %10s %10s %10s\n", "TABLE", "RECORDS", "MAX_ID", "SEQUENCE");
echo str_repeat("-", 80) . "\n";

// Show first 20 only
foreach (array_slice($isOk, 0, 20) as $info) {
    printf(
        "%-40s %10d %10d %10d ✓\n",
        $info['table'],
        $info['records'],
        $info['max_id'],
        $info['sequence']
    );
}

if (count($isOk) > 20) {
    echo "... and " . (count($isOk) - 20) . " more tables\n";
}

echo "\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ANALYSIS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$totalNeedsFix = count($needsFix);
$totalOk = count($isOk);
$totalAll = $totalNeedsFix + $totalOk;
$percentBroken = round(($totalNeedsFix / $totalAll) * 100, 1);

echo "Total tables: {$totalAll}\n";
echo "Need fixing: {$totalNeedsFix} ({$percentBroken}%)\n";
echo "Already OK: {$totalOk} (" . round(100 - $percentBroken, 1) . "%)\n\n";

echo "WHY THIS HAPPENS:\n";
echo "- pgloader inserts data with explicit IDs from MySQL\n";
echo "- PostgreSQL sequences are NOT called during INSERT with explicit ID\n";
echo "- Tables with existing data before sync → sequence stays at old value\n";
echo "- Tables that are empty or new → sequence works normally\n\n";

echo "RECOMMENDATION:\n";
echo "Always run fix-all-sequences-pgsql-postgres.php after EVERY pgloader sync!\n";

echo "</pre>\n";
