<?php
/**
 * Get table structure and export to JSON and TXT files
 * Usage: php get_table_struct.php table=vps_instances
 */

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Parse arguments
$tableName = null;
$force = false;
foreach ($argv as $arg) {
    if (strpos($arg, 'table=') === 0) {
        $tableName = substr($arg, 6);
    }
    if ($arg === 'force' || $arg === '--force' || $arg === '-f') {
        $force = true;
    }
}

if (!$tableName) {
    echo "Usage: php get_table_struct.php table=<tablename> [force]\n";
    echo "Example: php get_table_struct.php table=vps_instances\n";
    echo "         php get_table_struct.php table=all force\n";
    exit(1);
}

// Get list of tables
if ($tableName === 'all') {
    $tables = DB::select('SHOW TABLES');
    $dbName = DB::connection()->getDatabaseName();
    $tableNames = array_map(function($t) use ($dbName) {
        return $t->{"Tables_in_{$dbName}"};
    }, $tables);
    
    echo "Found " . count($tableNames) . " tables\n";
} else {
    $tableNames = [$tableName];
}

// Process each table
foreach ($tableNames as $currentTable) {
    processTable($currentTable, $force);
}

echo "\nAll done!\n";
exit(0);

function processTable($tableName, $force) {
    $outputDir = __DIR__ . '/table_struct';
    
    // Create directory if not exists
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }
    
    $txtFile = $outputDir . '/' . $tableName . '.txt';
    $sqlFile = $outputDir . '/' . $tableName . '.sql';

    // Check if table exists
    try {
        $columns = DB::select("DESCRIBE {$tableName}");
    } catch (Exception $e) {
        echo "Error: Table '{$tableName}' does not exist or cannot be accessed.\n";
        echo $e->getMessage() . "\n";
        return;
    }

    // Get CREATE TABLE statement
    try {
        $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
        $createTableSql = $createTable[0]->{'Create Table'};
} catch (Exception $e) {
    $createTableSql = null;
}

// Check if files exist and ask for overwrite
$existingFiles = [];
$allFiles = [$txtFile, $sqlFile];

foreach ($allFiles as $file) {
    if (file_exists($file)) {
        $existingFiles[] = basename($file);
    }
}

$filesToWrite = $allFiles;

if (!empty($existingFiles) && !$force) {
    echo "The following files already exist:\n";
    foreach ($existingFiles as $filename) {
        echo "  - {$filename}\n";
    }
    echo "Overwrite all? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $answer = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($answer) !== 'y') {
        echo "Aborted.\n";
        return;
    }
}

if (empty($filesToWrite)) {
    echo "No files to write for {$tableName}. Skipping.\n";
    return;
}

// Prepare data
$data = [];
foreach ($columns as $col) {
    $data[] = [
        'Field' => $col->Field,
        'Type' => $col->Type,
        'Null' => $col->Null,
        'Key' => $col->Key,
        'Default' => $col->Default,
        'Extra' => $col->Extra,
    ];
}

// Write TXT file (formatted table)
if (in_array($txtFile, $filesToWrite)) {
    $txtContent = "-- Table: {$tableName}\n";
    $txtContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Calculate column widths
    $widths = ['Field' => 5, 'Type' => 4, 'Null' => 4, 'Key' => 3, 'Default' => 7, 'Extra' => 5];
    foreach ($data as $row) {
        foreach ($widths as $key => $width) {
            $widths[$key] = max($width, strlen($row[$key] ?? ''));
        }
    }
    
    // Build separator
    $sep = '+';
    foreach ($widths as $w) {
        $sep .= str_repeat('-', $w + 2) . '+';
    }
    $sep .= "\n";
    
    // Build header
    $header = '|';
    foreach ($widths as $key => $w) {
        $header .= ' ' . str_pad($key, $w) . ' |';
    }
    $header .= "\n";
    
    $txtContent .= $sep . $header . $sep;
    
    // Build rows
    foreach ($data as $row) {
        $line = '|';
        foreach ($widths as $key => $w) {
            $line .= ' ' . str_pad($row[$key] ?? '', $w) . ' |';
        }
        $line .= "\n";
        $txtContent .= $line;
    }
    
    $txtContent .= $sep;
    $txtContent .= "\n" . count($data) . " columns\n";
    
    file_put_contents($txtFile, $txtContent);
    echo "Created: " . basename($txtFile) . "\n";
}

// Write SQL file
if (in_array($sqlFile, $filesToWrite) && $createTableSql) {
    $sqlContent = "-- Table: {$tableName}\n";
    $sqlContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlContent .= $createTableSql . ";\n";
    
    file_put_contents($sqlFile, $sqlContent);
    echo "Created: " . basename($sqlFile) . "\n";
}
}
