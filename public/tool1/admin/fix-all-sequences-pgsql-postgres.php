<?php
/**
 * Fix all PostgreSQL sequences after pgloader import from MySQL
 * This script finds all tables with auto-increment columns and fixes their sequences
 */

$GLOBALS['DISABLE_DEBUG_BAR'] = 1;
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "/var/www/html/public/index.php";


if(!isAdminCookie()){
    die("Access denied. Admins only.");
}

/**
 * Fix all PostgreSQL sequences to match MAX(id) values
 *
 * @param bool $verbose Whether to print detailed output (default: true)
 * @return array ['fixed' => int, 'skipped' => int, 'errors' => int, 'details' => array]
 */
function fixAllSequences($verbose = true) {
    $results = [
        'fixed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'details' => []
    ];

    // Get all tables with sequences - using subquery to safely handle pg_get_serial_sequence
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

    if ($verbose) {
        echo "Found " . count($tables) . " tables with sequences:\n\n";
    }

    foreach ($tables as $table) {
        $tableInfo = [
            'table' => $table->table_name,
            'column' => $table->column_name,
            'sequence' => null,
            'status' => 'unknown'
        ];

        if ($verbose) {
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "Table: {$table->table_name}\n";
            echo "Column: {$table->column_name}\n";
        }

        try {
            // Get sequence name safely - check if table exists first
            $fullTableName = $table->table_schema . '.' . $table->table_name;

            // Try to get sequence name
            $seqResult = \Illuminate\Support\Facades\DB::select("
                SELECT pg_get_serial_sequence(?, ?) as sequence_name
            ", [$fullTableName, $table->column_name]);

            $sequenceName = $seqResult[0]->sequence_name ?? null;
            $tableInfo['sequence'] = $sequenceName;

            if ($verbose) {
                echo "Sequence: " . ($sequenceName ?: 'NULL') . "\n";
            }

            if (empty($sequenceName)) {
                if ($verbose) echo "⚠️  No sequence found (might be dropped table)\n\n";
                $tableInfo['status'] = 'no_sequence';
                $results['skipped']++;
                $results['details'][] = $tableInfo;
                continue;
            }

            // Get current sequence value
            $currentSeq = \Illuminate\Support\Facades\DB::select("
                SELECT last_value, is_called FROM {$sequenceName}
            ");

            // Get MAX id from table
            $maxId = \Illuminate\Support\Facades\DB::select("
                SELECT MAX({$table->column_name}) as max_id, COUNT(*) as total
                FROM {$fullTableName}
            ");

            $currentValue = $currentSeq[0]->last_value ?? 0;
            $maxIdValue = $maxId[0]->max_id ?? 0;
            $totalRecords = $maxId[0]->total ?? 0;

            $tableInfo['current_value'] = $currentValue;
            $tableInfo['max_id'] = $maxIdValue;
            $tableInfo['total_records'] = $totalRecords;

            if ($verbose) {
                echo "Current sequence value: {$currentValue}\n";
                echo "MAX({$table->column_name}): {$maxIdValue}\n";
                echo "Total records: {$totalRecords}\n";
            }

            // Check if needs fixing
            if ($maxIdValue > $currentValue) {
                $newValue = $maxIdValue + 1;
                if ($verbose) echo "⚠️  MISMATCH! Fixing sequence to {$newValue}...\n";

                \Illuminate\Support\Facades\DB::statement("
                    SELECT setval(?, ?, false)
                ", [$sequenceName, $newValue]);

                // Verify
                $afterFix = \Illuminate\Support\Facades\DB::select("
                    SELECT last_value FROM {$sequenceName}
                ");

                $tableInfo['new_value'] = $afterFix[0]->last_value;
                $tableInfo['status'] = 'fixed';

                if ($verbose) echo "✅ FIXED! New value: {$afterFix[0]->last_value}\n";
                $results['fixed']++;
            } else {
                $tableInfo['status'] = 'ok';
                if ($verbose) echo "✓ OK - Sequence is correct\n";
                $results['skipped']++;
            }

        } catch (\Exception $e) {
            $tableInfo['status'] = 'error';
            $tableInfo['error'] = $e->getMessage();
            if ($verbose) {
                echo "❌ ERROR: " . $e->getMessage() . "\n";
                echo "   (This table might not exist or has been dropped)\n";
            }
            $results['errors']++;
        }

        $results['details'][] = $tableInfo;
        if ($verbose) echo "\n";
    }

    if ($verbose) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "\n<strong>SUMMARY:</strong>\n";
        echo "✅ Fixed: {$results['fixed']} sequences\n";
        echo "✓ OK/Skipped: {$results['skipped']} sequences\n";
        echo "❌ Errors: {$results['errors']} sequences\n";
        echo "\nTotal processed: " . count($tables) . " tables\n";
    }

    return $results;
}

// Run the function when accessed directly
try {
    echo "<h2>Fix All PostgreSQL Sequences</h2>\n";
    echo "<pre>\n";

    $results = fixAllSequences(true);

    echo "</pre>\n";

    // Return results as JSON if requested
    if (isset($_GET['json'])) {
        header('Content-Type: application/json');
        echo json_encode($results, JSON_PRETTY_PRINT);
        exit;
    }

} catch (\Throwable $e) {
    echo "<pre>FATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "</pre>";
}
