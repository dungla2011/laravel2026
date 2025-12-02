<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'monitor.mytree.vn';

require_once __DIR__.'/../../index.php';

// ========== MYSQL INT TO BIGINT MIGRATOR ==========
/*
UPDATE `file_clouds` set last_save_doc = NULL;
UPDATE `event_send_actions` set pushed_all_sms_to_queue = NULL;
UPDATE `order_items` set end_time = NULL;

-- 1. Đổi các trường sang NULL DEFAULT NULL;
ALTER TABLE `assets` CHANGE `purchase_date` `purchase_date` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `event_infos` CHANGE `time_start_check_in` `time_start_check_in` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `event_send_actions` CHANGE `last_force_send` `last_force_send` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `event_send_actions` CHANGE `pushed_all_sms_to_queue` `pushed_all_sms_to_queue` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `event_send_info_logs` CHANGE `last_app_sms_request_to_send` `last_app_sms_request_to_send` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `file_clouds` CHANGE `last_save_doc` `last_save_doc` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `hateco_certificates` CHANGE `ngay_sinh` `ngay_sinh` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `order_items` CHANGE `end_time` `end_time` TIMESTAMP NULL DEFAULT NULL;

-- 2. Xóa các constraints
ALTER TABLE `skus_product_variant_options` DROP FOREIGN KEY `spvo_product_variant_id_product_variants_id`;
ALTER TABLE `skus_product_variant_options` DROP FOREIGN KEY `skus_product_variant_options_sku_id_skus_id`;
ALTER TABLE `product_variants` DROP FOREIGN KEY `product_variants_product_id_products_id`;
ALTER TABLE `product_variant_options` DROP FOREIGN KEY `product_variant_options_product_variant_id_product_variants_id`;
//ALTER TABLE `product_variant_options` DROP FOREIGN KEY `product_variant_options_product_variant_id_product_roduct_variant_options`;
ALTER TABLE `skus_product_variant_options` DROP FOREIGN KEY `spvo_product_variant_options_id_product_variant_options_id`;
ALTER TABLE `skus` DROP FOREIGN KEY `skus_product_id_products_id`;

*/
class MySQLIntToBigintMigrator {
    private $connection;
    private $logFile;
    private $dbName;
    static public $allCmdError = [];

    static public $ignoreTable = ['rand_table', 'roles'];

    public function __construct($connectionName = null) {
        $this->connection = DB::connection($connectionName);
        $this->dbName = $this->connection->getDatabaseName();
        $this->logFile = __DIR__ . '/migration_mysql_' . date('Y-m-d_H-i-s') . '.log';

        $this->log("✅ Connected to MySQL database: {$this->dbName}");
        $this->log("📝 Log file: {$this->logFile}");
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        echo $logMessage;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function scanAndMigrate($dryRun = true) {
        $this->log("🔍 Starting INT(4-byte) to BIGINT migration scan...");
        $this->log("Mode: " . ($dryRun ? "DRY RUN (no changes)" : "EXECUTE CHANGES"));
        $this->log("Database: {$this->dbName}");
        $this->log("⚠️  Only targeting: INT, INTEGER (skipping MEDIUMINT, SMALLINT, TINYINT)");

        try {
            // Disable foreign key checks for migration
            if (!$dryRun) {
                $this->connection->statement('SET FOREIGN_KEY_CHECKS = 0');
                $this->log("⚠️  Foreign key checks disabled");
            }

            // Get all tables
            $tables = $this->getAllTables();
            $this->log("📋 Found " . count($tables) . " tables to scan");

            $totalChanges = 0;
            $failedTables = [];

            foreach ($tables as $table) {



//                getch("... Table  = $table");

                if(in_array($table, MySQLIntToBigintMigrator::$ignoreTable)){
                    echo "\n Ignore table $table";
                    continue;
                }
//                continue;

                $this->log("\n🔧 Scanning table: {$table}");
                try {
                    $changes = $this->processTable($table, $dryRun);
                    $totalChanges += $changes;
                } catch (Exception $e) {
                    $this->log("❌ Error processing table {$table}: " . $e->getMessage());
                    $failedTables[] = $table;
                }
            }

            // Re-enable foreign key checks
            if (!$dryRun) {
                $this->connection->statement('SET FOREIGN_KEY_CHECKS = 1');
                $this->log("✅ Foreign key checks re-enabled");
            }

            $this->log("\n📊 SUMMARY:");
            $this->log("Total tables scanned: " . count($tables));
            $this->log("Total INT(4-byte) fields to migrate: {$totalChanges}");
            $this->log("Failed tables: " . count($failedTables));

            if (!empty($failedTables)) {
                $this->log("Failed tables list: " . implode(', ', $failedTables));
            }

            if ($dryRun) {
                $this->log("⚠️  This was a DRY RUN - no changes were made");
                $this->log("💡 To execute changes, call with dryRun = false");
            } else {
                $this->log("✅ Migration completed!");
            }

        } catch (Exception $e) {
            $this->log("❌ Fatal error during migration: " . $e->getMessage());
            throw $e;
        }
    }

    private function getAllTables() {
        $sql = "
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = ?
            AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ";

        $tables = $this->connection->select($sql, [$this->dbName]);
        return array_column($tables, 'table_name');
    }

    private function processTable($tableName, $dryRun) {
        $intFields = $this->getIntegerFields($tableName);

        if (empty($intFields)) {
            $this->log("   ℹ️  No INT(4-byte) fields found");
            return 0;
        }

        $this->log("   📌 Found " . count($intFields) . " INT(4-byte) fields:");

        $changes = 0;

        foreach ($intFields as $field) {
            // ⭐ FIX: Convert stdClass to array or access as object
            $columnName = is_object($field) ? $field->COLUMN_NAME : $field['COLUMN_NAME'];
            $columnType = is_object($field) ? $field->COLUMN_TYPE : $field['COLUMN_TYPE'];
            $isNullable = is_object($field) ? $field->IS_NULLABLE : $field['IS_NULLABLE'];
            $columnDefault = is_object($field) ? $field->COLUMN_DEFAULT : $field['COLUMN_DEFAULT'];
            $extra = is_object($field) ? $field->EXTRA : $field['EXTRA'];

            $this->log("      - {$columnName} ({$columnType}) " .
                      ($isNullable === 'YES' ? 'NULL' : 'NOT NULL') .
                      ($columnDefault !== null ? " DEFAULT {$columnDefault}" : '') .
                      ($extra ? " {$extra}" : ''));

            if (!$dryRun) {
                $this->migrateField($tableName, $field);
            }
            $changes++;
        }

        return $changes;
    }

    private function getIntegerFields($tableName) {
        $sql = "
            SELECT
                COLUMN_NAME,
                COLUMN_TYPE,
                DATA_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                EXTRA,
                COLUMN_KEY
            FROM information_schema.columns
            WHERE table_schema = ?
            AND table_name = ?
            AND DATA_TYPE IN ('int', 'integer')
            AND COLUMN_TYPE NOT LIKE '%bigint%'
            ORDER BY ORDINAL_POSITION
        ";

        return $this->connection->select($sql, [$this->dbName, $tableName]);
    }

    private function migrateField($tableName, $field) {
        // ⭐ FIX: Handle object or array
        $columnName = is_object($field) ? $field->COLUMN_NAME : $field['COLUMN_NAME'];
        $currentType = is_object($field) ? $field->COLUMN_TYPE : $field['COLUMN_TYPE'];
        $sql = "";
        try {
            // Build new column definition
            $newType = $this->getNewColumnType($field);

            // Build ALTER statement
            $sql = "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$columnName}` {$newType}";

            $this->log("      🔄 Executing: {$sql}");
            $this->connection->statement($sql);

            $this->log("      ✅ Successfully migrated {$tableName}.{$columnName}");

        } catch (Exception $e) {

            self::$allCmdError[] = "\n" . $sql . "\n\n ---  ❌ Failed to migrate {$tableName}.{$columnName}: " . $e->getMessage() ;

            die("\n      ❌ Failed to migrate {$tableName}.{$columnName}: " . $e->getMessage());
            // getch("...");
            throw $e;
        }
    }

    private function getNewColumnType($field) {
        // ⭐ FIX: Handle object or array consistently
        $dataType = is_object($field) ? strtolower($field->DATA_TYPE) : strtolower($field['DATA_TYPE']);
        $columnType = is_object($field) ? strtolower($field->COLUMN_TYPE) : strtolower($field['COLUMN_TYPE']);
        $isNullable = is_object($field) ? ($field->IS_NULLABLE === 'YES') : ($field['IS_NULLABLE'] === 'YES');
        $default = is_object($field) ? $field->COLUMN_DEFAULT : $field['COLUMN_DEFAULT'];
        $extra = is_object($field) ? $field->EXTRA : $field['EXTRA'];

        // Determine if unsigned
        $unsigned = strpos($columnType, 'unsigned') !== false ? ' UNSIGNED' : '';

        // Build new type
        $newType = 'BIGINT' . $unsigned;

        // Add nullable
        $newType .= $isNullable ? ' NULL' : ' NOT NULL';

        // Add default
        if ($default !== null) {
            if (strtolower($default) === 'current_timestamp') {
                $newType .= " DEFAULT CURRENT_TIMESTAMP";
            } elseif (is_numeric($default)) {
                $newType .= " DEFAULT {$default}";
            } elseif (strtoupper($default) === 'NULL') {
                $newType .= " DEFAULT NULL";
            } else {
                $newType .= " DEFAULT '{$default}'";
            }
        } elseif ($isNullable) {
            $newType .= " DEFAULT NULL";
        }

        // Add extra (AUTO_INCREMENT, etc.)
        if ($extra) {
            $newType .= " {$extra}";
        }

        return $newType;
    }

    public function generateBackupScript() {
        $this->log("\n📋 Generating backup commands...");

        $backupFile = "backup_before_migration_" . date('Y-m-d_H-i-s') . ".sql";
        $backupCommand = "mysqldump -u USERNAME -p {$this->dbName} > {$backupFile}";

        $this->log("💾 Backup command:");
        $this->log("   {$backupCommand}");

        return $backupCommand;
    }

    public function validateMigration() {
        $this->log("\n🔍 Validating migration results...");

        $sql = "
            SELECT
                table_name,
                column_name,
                column_type,
                data_type
            FROM information_schema.columns
            WHERE table_schema = ?
            AND DATA_TYPE IN ('int', 'integer')
            AND COLUMN_TYPE NOT LIKE '%bigint%'
            ORDER BY table_name, column_name
        ";

        $remainingIntFields = $this->connection->select($sql, [$this->dbName]);

        if (empty($remainingIntFields)) {
            $this->log("✅ Validation passed: No INT(4-byte) fields remaining");
        } else {
            $this->log("⚠️  Validation warning: " . count($remainingIntFields) . " INT(4-byte) fields still exist:");
            foreach ($remainingIntFields as $field) {
                $this->log("   - {$field->table_name}.{$field->column_name} ({$field->column_type})");
            }
        }
    }

    public function showDatabaseInfo() {
        $this->log("\n📊 Database Information:");

        // Database size
        $sql = "
            SELECT
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
            FROM information_schema.tables
            WHERE table_schema = ?
        ";
        $result = $this->connection->select($sql, [$this->dbName]);
        $sizeMB = $result[0]->size_mb ?? 0;
        $this->log("Database size: {$sizeMB} MB");

        // Table count
        $sql = "
            SELECT COUNT(*) as count
            FROM information_schema.tables
            WHERE table_schema = ?
        ";
        $result = $this->connection->select($sql, [$this->dbName]);
        $tableCount = $result[0]->count ?? 0;
        $this->log("Total tables: {$tableCount}");

        // INT field count (4-byte only)
        $sql = "
            SELECT COUNT(*) as count
            FROM information_schema.columns
            WHERE table_schema = ?
            AND DATA_TYPE IN ('int', 'integer')
            AND COLUMN_TYPE NOT LIKE '%bigint%'
        ";
        $result = $this->connection->select($sql, [$this->dbName]);
        $intFieldCount = $result[0]->count ?? 0;
        $this->log("INT(4-byte) fields to migrate: {$intFieldCount}");

        // Show what we're skipping
        $sql = "
            SELECT COUNT(*) as count
            FROM information_schema.columns
            WHERE table_schema = ?
            AND DATA_TYPE IN ('mediumint', 'smallint', 'tinyint')
        ";
        $result = $this->connection->select($sql, [$this->dbName]);
        $skippedCount = $result[0]->count ?? 0;
        $this->log("MEDIUMINT/SMALLINT/TINYINT fields (skipped): {$skippedCount}");
    }
}



// ========== USAGE ==========
try {
    echo "🚀 MySQL INT(4-byte) to BIGINT Migration Tool\n";
    echo "==============================================\n";
    echo "📌 Target: INT, INTEGER only\n";
    echo "⏭️  Skip: MEDIUMINT, SMALLINT, TINYINT (will remain unchanged)\n\n";

    // Initialize migrator
    $migrator = new MySQLIntToBigintMigrator();

    // Show database info
    $migrator->showDatabaseInfo();

    // Generate backup command
    $migrator->generateBackupScript();

    echo "\n⚠️  IMPORTANT SAFETY STEPS:\n";
    echo "1. Backup your database first!\n";
    echo "2. Test on development environment\n";
    echo "3. Run DRY RUN first to see what will change\n";
    echo "4. Plan for maintenance window (tables will be locked briefly)\n";
    echo "5. Monitor for foreign key constraint issues\n\n";

    // Interactive mode
//    if (php_sapi_name() === 'cli') {
//        echo "Choose an option:\n";
//        echo "1. DRY RUN (scan only, no changes)\n";
//        echo "2. EXECUTE MIGRATION\n";
//        echo "3. VALIDATE EXISTING MIGRATION\n";
//        echo "Enter choice (1-3): ";
//
//        $choice = trim(fgets(STDIN));
//    } else {
//        // For web interface, default to dry run
//        $choice = '1';
//        echo "🔍 Running DRY RUN (web mode)...\n";
//    }

    $choice = 2;

    switch ($choice) {
        case '1':
            echo "\n🔍 Running DRY RUN...\n";
            $migrator->scanAndMigrate(true);
            break;

        case '2':
            if (php_sapi_name() === 'cli') {
//                echo "\n⚠️  Are you sure you want to EXECUTE the migration? (yes/no): ";
//                $confirm = trim(fgets(STDIN));

                // if (strtolower($confirm) === 'yes')
                if(1)
                {
//                    getch(".....");
                    echo "\n🔧 Executing migration...\n";
                    $migrator->scanAndMigrate(false);
//                    getch(".....222");

                    $migrator->validateMigration();
                } else {
                    echo "❌ Migration cancelled\n";
                }
            } else {
                echo "❌ Execute mode only available in CLI\n";
            }
            break;

        case '3':
            echo "\n🔍 Validating migration...\n";
            $migrator->validateMigration();
            break;

        default:
            echo "❌ Invalid choice\n";
            break;
    }


    $str = implode(";\n", MySQLIntToBigintMigrator::$allCmdError);

    echo "⚠️  Migration errors found:\n";
    echo $str . "\n";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✅ Script completed\n";

?>
