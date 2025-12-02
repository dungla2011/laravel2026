<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
use Illuminate\Support\Facades\DB;

// Tables to export from MySQL
$tablesToExport = ['menu_trees', 'model_meta_infos', 'role_user', 'roles'];

echo "=== Exporting Tables from MySQL to SQLite ===\n\n";

$sqlStatements = [];

foreach ($tablesToExport as $table) {
    echo "Exporting table: $table\n";

    try {
        // Get all data from MySQL
        $data = DB::connection('mysql')->table($table)->get();

        if ($data->isEmpty()) {
            echo "  -> Table is empty, skipping...\n\n";
            continue;
        }

        // Get column names
        $columns = array_keys((array)$data[0]);
        $columnList = implode(', ', array_map(fn($col) => "`$col`", $columns));

        // Build INSERT statements
        $insertCount = 0;
        foreach ($data as $row) {
            $values = [];
            foreach ($columns as $col) {
                $value = $row->$col;
                if ($value === null) {
                    $values[] = 'NULL';
                } else if (is_numeric($value) && !is_string($row->$col)) {
                    $values[] = $value;
                } else {
                    $values[] = "'" . addslashes($value) . "'";
                }
            }
            $valueList = implode(', ', $values);
            $sqlStatements[] = "INSERT INTO `$table` ($columnList) VALUES ($valueList);";
            $insertCount++;
        }

        echo "  -> Exported $insertCount rows\n\n";

    } catch (\Exception $e) {
        echo "  -> ERROR: " . $e->getMessage() . "\n\n";
    }
}

// Write to SQL file
$sqlFile = '/share/mysql_to_sqlite.sql';
@mkdir(dirname($sqlFile), 0755, true);

$sqlContent = "-- Exported from MySQL\n";
$sqlContent .= "-- Date: " . now()->format('Y-m-d H:i:s') . "\n\n";
$sqlContent .= implode("\n", $sqlStatements);

file_put_contents($sqlFile, $sqlContent);

echo "✅ SQL file created: " . realpath($sqlFile) . "\n";
echo "Total statements: " . count($sqlStatements) . "\n\n";

// Now import to SQLite
echo "=== Importing to SQLite ===\n\n";
//
//try {
//    $statements = array_filter(explode(';', $sqlContent), fn($s) => trim($s));
//
//    foreach ($statements as $statement) {
//        $stmt = trim($statement);
//        if (!empty($stmt)) {
//            DB::connection('sqlite')->statement($stmt . ';');
//        }
//    }
//
//    echo "✅ Successfully imported all data to SQLite!\n";
//
//} catch (\Exception $e) {
//    echo "❌ ERROR during import: " . $e->getMessage() . "\n";
//}

echo "\nDone!\n";

