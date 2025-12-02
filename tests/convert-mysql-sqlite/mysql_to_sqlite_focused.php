<?php
/**
 * MySQL to SQLite Focused Converter
 * Only imports specific tables
 */

if ($argc < 2) {
    echo "Usage: php mysql_to_sqlite_focused.php <mysql_sql_file> [output_db_file]\n";
    exit(1);
}

$mysqlFile = $argv[1];
$dbFile = $argv[2] ?? 'glx_v3_focused.db';

if (!file_exists($mysqlFile)) {
    echo "Error: Input file '$mysqlFile' not found\n";
    exit(1);
}

// Tables to import
$targetTables = [
    'role_user',
    'model_meta_infos',
    'permissions',
    'demo_tbls',
    'permission_role',
    'menu_trees',
    'demo_folder_tbls'
];

echo "=" . str_repeat("=", 58) . "\n";
echo "MySQL to SQLite Focused Converter\n";
echo "=" . str_repeat("=", 58) . "\n\n";

// Step 1: Read and convert MySQL SQL
echo "[Step 1] Reading MySQL SQL file...\n";
$sqlContent = file_get_contents($mysqlFile);
echo "  Original size: " . number_format(strlen($sqlContent)) . " bytes\n\n";

echo "[Step 2] Converting MySQL syntax to SQLite...\n";

// Remove MySQL specific commands
$sqlContent = preg_replace('/^SET SQL_MODE.*?;/mi', '', $sqlContent);
$sqlContent = preg_replace('/^START TRANSACTION;/mi', '', $sqlContent);
$sqlContent = preg_replace('/^SET time_zone.*?;/mi', '', $sqlContent);
$sqlContent = preg_replace('/^\/\*!40[0-9]{3}.*?\*\//mi', '', $sqlContent);
$sqlContent = preg_replace('/^COMMIT;/mi', '', $sqlContent);
$sqlContent = preg_replace('/\/\*!40[0-9]{3}.*?\*\//i', '', $sqlContent);

// Remove AUTO_INCREMENT definitions
$sqlContent = preg_replace('/AUTO_INCREMENT=\d+/i', '', $sqlContent);

// Remove KEY definitions that SQLite doesn't support in CREATE TABLE
$sqlContent = preg_replace('/,\s*KEY\s+`[^`]*`\s*\([^)]+\)/i', '', $sqlContent);
$sqlContent = preg_replace('/,\s*UNIQUE\s+KEY\s+`[^`]*`\s*\([^)]+\)/i', '', $sqlContent);
$sqlContent = preg_replace('/,\s*FULLTEXT\s+KEY\s+`[^`]*`\s*\([^)]+\)/i', '', $sqlContent);
$sqlContent = preg_replace('/,\s*CONSTRAINT\s+`[^`]*`\s+FOREIGN\s+KEY[^;]+/i', '', $sqlContent);

// Remove CHECK constraints with function calls (like json_valid)
$sqlContent = preg_replace('/\s*CHECK\s*\(\s*json_valid\s*\([^)]*\)\s*\)/i', '', $sqlContent);
$sqlContent = preg_replace('/\s*CHECK\s*\(\s*json\s*\([^)]*\)\s*\)/i', '', $sqlContent);

// Convert MySQL types to SQLite
$sqlContent = preg_replace('/\bBIGINT\s*\(\d+\)\s+UNSIGNED/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bBIGINT\s+UNSIGNED/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bBIGINT\s*\(\d+\)/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bBIGINT\b/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bINT\s*\(\d+\)\s+UNSIGNED/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bINT\s+UNSIGNED/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bINT\s*\(\d+\)/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bSMALLINT\s*\(\d+\)\s+UNSIGNED/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bSMALLINT\s+UNSIGNED/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bSMALLINT\s*\(\d+\)/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bSMALLINT\b/i', 'INTEGER', $sqlContent);
$sqlContent = preg_replace('/\bTIMESTAMP\b/i', 'DATETIME', $sqlContent);
$sqlContent = preg_replace('/\bLONGTEXT\b/i', 'TEXT', $sqlContent);
$sqlContent = preg_replace('/\bMEDIUMTEXT\b/i', 'TEXT', $sqlContent);
$sqlContent = preg_replace('/\bVARCHAR\s*\(\d+\)/i', 'TEXT', $sqlContent);
// Remove remaining UNSIGNED
$sqlContent = preg_replace('/\s+UNSIGNED/i', '', $sqlContent);

// Remove CHARACTER SET and COLLATE from column definitions
$sqlContent = preg_replace('/\s+CHARACTER\s+SET\s+[\w_]+/i', '', $sqlContent);
$sqlContent = preg_replace('/\s+COLLATE\s+[\w_]+/i', '', $sqlContent);

// Remove MySQL-specific ON UPDATE clauses
$sqlContent = preg_replace('/\s+ON\s+UPDATE\s+current_timestamp\(\)/i', '', $sqlContent);

// Remove MySQL-specific clauses at table level
$sqlContent = preg_replace('/\s+ENGINE=[\w_-]+/i', '', $sqlContent);
$sqlContent = preg_replace('/\s+DEFAULT\s+CHARSET=[\w_-]+/i', '', $sqlContent);
$sqlContent = preg_replace('/\s+COLLATE\s*=\s*[\w_]+/i', '', $sqlContent);
$sqlContent = preg_replace('/\s+COMMENT\s*=?\s*[\'"]?[^\'"\n;]*[\'"]?(?=\s|;|$)/i', '', $sqlContent);

// Remove SET DEFAULT clauses
$sqlContent = preg_replace('/,\s*SET\s+DEFAULT\s+[^\),]+/i', '', $sqlContent);

// Fix TIMESTAMP DEFAULT
$sqlContent = preg_replace('/DEFAULT\s+current_timestamp\(\)/i', 'DEFAULT CURRENT_TIMESTAMP', $sqlContent);

// Remove all backticks from identifiers and replace with double quotes (SQLite compatible)
// This preserves reserved keywords as valid identifiers
$sqlContent = preg_replace_callback(
    '/`([^`]+)`/',
    function($matches) {
        return '"' . $matches[1] . '"';
    },
    $sqlContent
);

// Clean up double commas and parentheses
$sqlContent = preg_replace('/,\s*,/i', ',', $sqlContent);
$sqlContent = preg_replace('/\)\s*\),/i', '),', $sqlContent);
$sqlContent = preg_replace('/\)\s*\)$/', ')', $sqlContent);

// Remove double spacing and excessive newlines
$sqlContent = preg_replace('/  +/', ' ', $sqlContent);
$sqlContent = preg_replace('/\n\n+/', "\n", $sqlContent);

echo "✓ Converted\n\n";

// Step 3: Import into SQLite
echo "[Step 3] Creating tables and importing data...\n";

try {
    // Remove old database
    if (file_exists($dbFile)) {
        unlink($dbFile);
    }

    // Create connection
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set pragmas
    $pdo->exec("PRAGMA foreign_keys = ON");
    $pdo->exec("PRAGMA journal_mode = WAL");

    // Parse SQL statements
    $lines = explode("\n", $sqlContent);
    $statements = [];
    $currentStmt = '';
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip empty lines and comments
        if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
            continue;
        }
        
        $currentStmt .= ' ' . $line;
        
        // Check if this line ends with semicolon
        if (substr($line, -1) === ';') {
            // Remove semicolon and trim
            $stmt = trim(substr($currentStmt, 0, -1));
            if (!empty($stmt)) {
                $statements[] = $stmt;
            }
            $currentStmt = '';
        }
    }
    
    // Add any remaining statement
    if (!empty(trim($currentStmt))) {
        $statements[] = trim($currentStmt);
    }

    $tableCount = 0;
    $insertCount = 0;
    $errors = 0;

    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        // Skip comments and empty statements
        if (empty($statement) || substr($statement, 0, 2) === '--' || substr($statement, 0, 3) === '/*!') {
            continue;
        }

        try {
            // Skip certain statements
            if (preg_match('/^(PRAGMA|SET|SHOW)/i', $statement)) {
                continue;
            }

            // Check if it's CREATE TABLE or INSERT
            if (preg_match('/^CREATE\s+TABLE\s+(?:")?(\w+)(?:")?/i', $statement, $matches)) {
                $tableName = $matches[1];
                
                // Only create tables in our target list
                if (in_array($tableName, $targetTables)) {
                    $pdo->exec($statement);
                    $tableCount++;
                    echo "  ✓ Table '$tableName' created\n";
                }
            } elseif (preg_match('/^INSERT\s+INTO\s+(?:")?(\w+)(?:")?/i', $statement, $matches)) {
                $tableName = $matches[1];
                
                // Only insert into tables in our target list
                if (in_array($tableName, $targetTables)) {
                    $pdo->exec($statement);
                    $insertCount++;
                    if ($insertCount % 100 === 0) {
                        echo "  ✓ Inserted $insertCount records...\n";
                    }
                }
            }
        } catch (Exception $e) {
            $errors++;
            if ($errors <= 20) {
                $shortStmt = substr($statement, 0, 100);
                echo "  ⚠ Error in '$tableName': " . $shortStmt . "...\n";
                echo "    " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✓ CONVERSION COMPLETED!\n";
    echo str_repeat("=", 60) . "\n";
    echo "Database: $dbFile\n";
    if (file_exists($dbFile)) {
        echo "Size: " . number_format(filesize($dbFile)) . " bytes\n";
    }
    echo "Tables created: $tableCount\n";
    echo "Records inserted: " . number_format($insertCount) . "\n";
    echo "Errors: $errors\n";
    echo "\nUsage:\n";
    echo "  sqlite3 $dbFile\n";
    echo "  Or in PHP: \$pdo = new PDO('sqlite:$dbFile');\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
