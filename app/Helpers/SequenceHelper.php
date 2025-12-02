<?php
/**
 * Function to fix all PostgreSQL sequences
 * Can be called from anywhere in the application
 */

if (!function_exists('fixAllPostgresSequences')) {
    /**
     * Fix all PostgreSQL sequences after import/sync from MySQL
     * 
     * @param bool $verbose - Show detailed output
     * @param bool $dryRun - Just check, don't fix
     * @return array Statistics of the operation
     */
    function fixAllPostgresSequences($verbose = true, $dryRun = false)
    {
        $stats = [
            'total' => 0,
            'fixed' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => []
        ];

        try {
            // Get all tables with sequences
            $tables = \Illuminate\Support\Facades\DB::select("
                SELECT 
                    t.table_name,
                    c.column_name,
                    pg_get_serial_sequence(t.table_schema || '.' || t.table_name, c.column_name) as sequence_name
                FROM information_schema.tables t
                JOIN information_schema.columns c 
                    ON t.table_name = c.table_name 
                    AND t.table_schema = c.table_schema
                WHERE t.table_schema NOT IN ('pg_catalog', 'information_schema')
                    AND c.column_default LIKE 'nextval%'
                ORDER BY t.table_name
            ");

            $stats['total'] = count($tables);

            if ($verbose) {
                echo "\n" . str_repeat("=", 60) . "\n";
                echo "FIX ALL POSTGRESQL SEQUENCES\n";
                echo str_repeat("=", 60) . "\n";
                echo "Found {$stats['total']} tables with sequences\n";
                if ($dryRun) {
                    echo "⚠️  DRY RUN MODE - No changes will be made\n";
                }
                echo "\n";
            }

            foreach ($tables as $table) {
                $detail = [
                    'table' => $table->table_name,
                    'column' => $table->column_name,
                    'sequence' => $table->sequence_name,
                    'status' => 'skipped'
                ];

                if ($verbose) {
                    echo str_repeat("-", 60) . "\n";
                    echo "📋 Table: {$table->table_name}\n";
                    echo "   Column: {$table->column_name}\n";
                    echo "   Sequence: {$table->sequence_name}\n";
                }

                if (empty($table->sequence_name)) {
                    if ($verbose) echo "   ❌ No sequence found\n\n";
                    $detail['status'] = 'no_sequence';
                    $stats['skipped']++;
                    $stats['details'][] = $detail;
                    continue;
                }

                try {
                    // Get current sequence value
                    $currentSeq = \Illuminate\Support\Facades\DB::select("
                        SELECT last_value, is_called FROM {$table->sequence_name}
                    ");

                    // Get MAX id from table
                    $maxId = \Illuminate\Support\Facades\DB::select("
                        SELECT MAX({$table->column_name}) as max_id, COUNT(*) as total 
                        FROM {$table->table_name}
                    ");

                    $currentValue = $currentSeq[0]->last_value ?? 0;
                    $maxIdValue = $maxId[0]->max_id ?? 0;
                    $totalRecords = $maxId[0]->total ?? 0;

                    $detail['current_seq'] = $currentValue;
                    $detail['max_id'] = $maxIdValue;
                    $detail['total_records'] = $totalRecords;

                    if ($verbose) {
                        echo "   Current: {$currentValue} | MAX: {$maxIdValue} | Records: {$totalRecords}\n";
                    }

                    // Check if needs fixing
                    if ($maxIdValue > $currentValue) {
                        $newValue = $maxIdValue + 1;
                        $detail['new_value'] = $newValue;
                        $detail['diff'] = $maxIdValue - $currentValue;

                        if ($verbose) {
                            echo "   ⚠️  MISMATCH! (diff: " . $detail['diff'] . ")\n";
                        }

                        if (!$dryRun) {
                            \Illuminate\Support\Facades\DB::statement("
                                SELECT setval('{$table->sequence_name}', {$newValue}, false)
                            ");

                            // Verify
                            $afterFix = \Illuminate\Support\Facades\DB::select("
                                SELECT last_value FROM {$table->sequence_name}
                            ");

                            if ($verbose) {
                                echo "   ✅ FIXED! New value: {$afterFix[0]->last_value}\n";
                            }

                            $detail['status'] = 'fixed';
                            $detail['verified_value'] = $afterFix[0]->last_value;
                            $stats['fixed']++;
                        } else {
                            if ($verbose) {
                                echo "   🔍 Would fix to: {$newValue}\n";
                            }
                            $detail['status'] = 'would_fix';
                            $stats['fixed']++;
                        }
                    } else {
                        if ($verbose) {
                            echo "   ✓ OK - Sequence is correct\n";
                        }
                        $detail['status'] = 'ok';
                        $stats['skipped']++;
                    }

                } catch (\Exception $e) {
                    if ($verbose) {
                        echo "   ❌ ERROR: " . $e->getMessage() . "\n";
                    }
                    $detail['status'] = 'error';
                    $detail['error'] = $e->getMessage();
                    $stats['errors']++;
                }

                $stats['details'][] = $detail;
                if ($verbose) echo "\n";
            }

            if ($verbose) {
                echo str_repeat("=", 60) . "\n";
                echo "SUMMARY:\n";
                echo str_repeat("=", 60) . "\n";
                echo "Total tables:    {$stats['total']}\n";
                echo "✅ Fixed:        {$stats['fixed']}\n";
                echo "✓  OK/Skipped:   {$stats['skipped']}\n";
                echo "❌ Errors:       {$stats['errors']}\n";
                echo str_repeat("=", 60) . "\n";
            }

        } catch (\Throwable $e) {
            if ($verbose) {
                echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
            }
            $stats['fatal_error'] = $e->getMessage();
        }

        return $stats;
    }
}

if (!function_exists('fixSequenceForTable')) {
    /**
     * Fix sequence for a specific table
     * 
     * @param string $tableName - Table name
     * @param string $columnName - Column name (default: 'id')
     * @param bool $verbose - Show output
     * @return bool Success
     */
    function fixSequenceForTable($tableName, $columnName = 'id', $verbose = true)
    {
        try {
            // Get sequence name
            $sequenceName = \Illuminate\Support\Facades\DB::select("
                SELECT pg_get_serial_sequence('{$tableName}', '{$columnName}') as sequence_name
            ");

            if (empty($sequenceName[0]->sequence_name)) {
                if ($verbose) echo "❌ No sequence found for {$tableName}.{$columnName}\n";
                return false;
            }

            $seqName = $sequenceName[0]->sequence_name;

            // Get MAX id
            $maxId = \Illuminate\Support\Facades\DB::select("
                SELECT MAX({$columnName}) as max_id FROM {$tableName}
            ");

            $maxIdValue = $maxId[0]->max_id ?? 0;
            $newValue = $maxIdValue + 1;

            // Fix sequence
            \Illuminate\Support\Facades\DB::statement("
                SELECT setval('{$seqName}', {$newValue}, false)
            ");

            if ($verbose) {
                echo "✅ Fixed sequence for {$tableName}: {$seqName} → {$newValue}\n";
            }

            return true;

        } catch (\Exception $e) {
            if ($verbose) {
                echo "❌ Error fixing {$tableName}: " . $e->getMessage() . "\n";
            }
            return false;
        }
    }
}
