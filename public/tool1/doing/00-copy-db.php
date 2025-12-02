<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


// $_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

// ========== DATABASE COPY SCRIPT ==========

class DatabaseCopier {
    private $sourceDb = 'glx2022db';
    private $targetDb = 'glx_2025_mytree';
    private $charset = 'utf8mb4';
    private $collation = 'utf8mb4_unicode_ci';
    private $structureOnlyTables = ['rand_table', 'log_users', 'change_logs'];
    private $skipTables = ['telescope_entries']; // Bảng bỏ qua hoàn toàn

    public function __construct() {
        echo "🚀 Database Copy Tool: {$this->sourceDb} -> {$this->targetDb}\n";
        echo "=" . str_repeat("=", 60) . "\n\n";
    }

    public function deleteDbOld()
    {
        //Hỏi cli trước khi xoá:
        if (php_sapi_name() === 'cli') {
            echo "⚠️ Bạn có chắc muốn xoá database {$this->targetDb}? (y/n): ";
            $confirm = trim(fgets(STDIN));
            if (strtolower($confirm) !== 'y') {
                echo "❌ Xoá database bị hủy\n";
                return false;
            }
        } else {
            // Web mode - tự động xoá
            echo "🔍 Chạy xoá database trong web mode...\n";
        }
        echo "🗑️ Xoá database {$this->targetDb} nếu đã tồn tại...\n";

        try {
            DB::statement("DROP DATABASE IF EXISTS `{$this->targetDb}`");
            echo "✅ Database {$this->targetDb} đã được xoá (nếu tồn tại)\n\n";
        } catch (Exception $e) {
            echo "❌ Lỗi khi xoá database: " . $e->getMessage() . "\n";
            return false;
        }

    }

    public function copyDatabase($dryRun = true) {
        try {
            // Tạo database target nếu chưa có
            $this->createTargetDatabase();

            // Lấy danh sách bảng từ source
            $tables = $this->getSourceTables();

            if (empty($tables)) {
                echo "❌ Không tìm thấy bảng nào trong database {$this->sourceDb}\n";
                return false;
            }

            echo "📋 Tìm thấy " . count($tables) . " bảng trong {$this->sourceDb}\n\n";

            if ($dryRun) {
                echo "🔍 DRY RUN MODE - Chỉ hiển thị preview\n";
                $this->previewCopy($tables);
                return true;
            }

            // Thực hiện copy
            $this->executeCopy($tables);

            echo "🎉 Hoàn thành copy database!\n";
            return true;

        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function createTargetDatabase() {
        echo "🔧 Tạo database {$this->targetDb} nếu chưa có...\n";

        $sql = "CREATE DATABASE IF NOT EXISTS `{$this->targetDb}`
                CHARACTER SET {$this->charset}
                COLLATE {$this->collation}";

        DB::statement($sql);
        echo "✅ Database {$this->targetDb} đã sẵn sàng\n\n";
    }

    private function getSourceTables() {
        $sql = "SELECT TABLE_NAME
                FROM information_schema.tables
                WHERE table_schema = '{$this->sourceDb}'
                AND table_type = 'BASE TABLE'
                ORDER BY TABLE_NAME";

        $results = DB::select($sql);
        $allTables = array_column($results, 'TABLE_NAME');

        // Lọc bỏ các bảng trong skipTables
        $filteredTables = array_filter($allTables, function($table) {
            return !in_array($table, $this->skipTables);
        });

        return array_values($filteredTables);
    }

    // FIX: Thêm method getTargetTables() bị thiếu
    private function getTargetTables() {
        $sql = "SELECT TABLE_NAME
                FROM information_schema.tables
                WHERE table_schema = '{$this->targetDb}'
                AND table_type = 'BASE TABLE'
                ORDER BY TABLE_NAME";

        try {
            $results = DB::select($sql);
            return array_column($results, 'TABLE_NAME');
        } catch (Exception $e) {
            // Database target chưa tồn tại hoặc chưa có bảng nào
            return [];
        }
    }

    private function previewCopy($tables) {
        echo "📝 PREVIEW - Các bảng sẽ được copy:\n";
        echo "-" . str_repeat("-", 50) . "\n";

        $fullCopyCount = 0;
        $structureOnlyCount = 0;

        foreach ($tables as $table) {
            $isStructureOnly = in_array($table, $this->structureOnlyTables);
            $mode = $isStructureOnly ? "STRUCTURE ONLY" : "FULL COPY";
            $icon = $isStructureOnly ? "📋" : "📦";

            echo "{$icon} {$table} ({$mode})\n";

            if ($isStructureOnly) {
                $structureOnlyCount++;
            } else {
                $fullCopyCount++;
            }
        }

        echo "\n📊 Tổng kết:\n";
        echo "   - Full copy (cấu trúc + dữ liệu): {$fullCopyCount} bảng\n";
        echo "   - Structure only: {$structureOnlyCount} bảng\n";
        echo "   - Tổng cộng: " . count($tables) . " bảng\n";

        // Hiển thị bảng bị bỏ qua
        if (!empty($this->skipTables)) {
            echo "   - Bỏ qua: " . count($this->skipTables) . " bảng\n";
            echo "\n🚫 Bảng bỏ qua:\n";
            foreach ($this->skipTables as $table) {
                echo "   ❌ {$table}\n";
            }
        }
        echo "\n";
    }

    private function executeCopy($tables) {
        echo "🔄 Bắt đầu copy database...\n";
        echo "-" . str_repeat("-", 50) . "\n";

        $successCount = 0;
        $errorCount = 0;
        $errorTables = []; // Lưu danh sách bảng lỗi

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            try {
                $isStructureOnly = in_array($table, $this->structureOnlyTables);

                if ($isStructureOnly) {
                    $this->copyTableStructure($table);
                } else {
                    $this->copyTableFull($table);
                }

                $successCount++;

            } catch (Exception $e) {
                echo "❌ Lỗi copy {$table}: " . $e->getMessage() . "\n";
                $errorCount++;
                $errorTables[] = $table; // Thêm vào danh sách lỗi
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        echo "\n📊 KẾT QUẢ:\n";
        echo "   - Thành công: {$successCount} bảng\n";
        echo "   - Lỗi: {$errorCount} bảng\n";

        if (!empty($this->skipTables)) {
            echo "   - Bỏ qua: " . count($this->skipTables) . " bảng\n";
        }

        // Hiển thị danh sách bảng lỗi
        if (!empty($errorTables)) {
            echo "\n❌ DANH SÁCH BẢNG BỊ LỖI:\n";
            foreach ($errorTables as $table) {
                echo "   🔴 {$table}\n";
            }
            echo "\n💡 Khuyến nghị: Kiểm tra lại cấu trúc hoặc dữ liệu của các bảng trên\n";

            // Hiển thị chi tiết lỗi
            $this->showErrorSummary($errorTables);
        }

    }

    private function copyTableStructure($table) {
        echo "📋 Copy structure: {$table}... ";

        try {
            // Lấy CREATE TABLE statement
            $createTableResult = DB::select("SHOW CREATE TABLE `{$this->sourceDb}`.`{$table}`");

            if (empty($createTableResult)) {
                throw new Exception("Không thể lấy cấu trúc bảng {$table}");
            }

            $createTableSql = $createTableResult[0]->{'Create Table'};

            // Thay đổi để tạo trong target database
            $createTableSql = str_replace("CREATE TABLE `{$table}`",
                                     "CREATE TABLE `{$this->targetDb}`.`{$table}`",
                                     $createTableSql);

            // Drop table nếu đã tồn tại
            DB::statement("DROP TABLE IF EXISTS `{$this->targetDb}`.`{$table}`");

            // Tạo table
            DB::statement($createTableSql);

            echo "✅ Hoàn thành\n";

        } catch (Exception $e) {
            echo "❌ Lỗi\n";
            throw new Exception("Lỗi copy structure bảng {$table}: " . $e->getMessage());
        }
    }

    private function copyTableFull($table) {
        echo "📦 Copy full: {$table}... ";

        try {
            // Copy structure first
            $this->copyTableStructure($table);

            // Copy data với error handling
            $sql = "INSERT INTO `{$this->targetDb}`.`{$table}`
                    SELECT * FROM `{$this->sourceDb}`.`{$table}`";

            DB::statement($sql);

            // Get record count
            $count = DB::select("SELECT COUNT(*) as count FROM `{$this->targetDb}`.`{$table}`")[0]->count;

            echo "✅ Hoàn thành ({$count} bản ghi)\n";

        } catch (Exception $e) {
            echo "❌ Lỗi\n";
            throw new Exception("Lỗi copy data bảng {$table}: " . $e->getMessage());
        }
    }

    public function validateCopy() {
        echo "🔍 Kiểm tra kết quả copy...\n";
        echo "-" . str_repeat("-", 40) . "\n";

        $sourceTables = $this->getSourceTables();
        $targetTables = $this->getTargetTables();

        $missingTables = array_diff($sourceTables, $targetTables);

        if (empty($missingTables)) {
            echo "✅ Tất cả bảng đã được copy\n";
        } else {
            echo "❌ Thiếu " . count($missingTables) . " bảng:\n";
            foreach ($missingTables as $table) {
                echo "   🔴 {$table}\n";
            }
        }

        // So sánh số lượng bản ghi
        echo "\n📊 So sánh số lượng bản ghi:\n";
        $mismatchTables = [];

        foreach ($targetTables as $table) {
            $result = $this->compareTableCounts($table);
            if ($result === false) {
                $mismatchTables[] = $table;
            }
        }

        // Tổng kết validation
        if (!empty($missingTables) || !empty($mismatchTables)) {
            echo "\n⚠️  CÁC VẤN ĐỀ PHÁT HIỆN:\n";

            if (!empty($missingTables)) {
                echo "🔴 Bảng chưa được copy: " . implode(', ', $missingTables) . "\n";
            }

            if (!empty($mismatchTables)) {
                echo "🔴 Bảng có số lượng bản ghi không khớp: " . implode(', ', $mismatchTables) . "\n";
            }
        } else {
            echo "\n✅ Validation hoàn tất - Không có vấn đề!\n";
        }
    }

    private function compareTableCounts($table) {
        try {
            $sourceCount = DB::select("SELECT COUNT(*) as count FROM `{$this->sourceDb}`.`{$table}`")[0]->count;
            $targetCount = DB::select("SELECT COUNT(*) as count FROM `{$this->targetDb}`.`{$table}`")[0]->count;

            $isStructureOnly = in_array($table, $this->structureOnlyTables);
            $expectedTarget = $isStructureOnly ? 0 : $sourceCount;

            if ($targetCount == $expectedTarget) {
                $status = "✅";
                echo "   {$status} {$table}: {$sourceCount} -> {$targetCount}";
                $isValid = true;
            } else {
                $status = "❌";
                echo "   {$status} {$table}: {$sourceCount} -> {$targetCount} (KHÔNG KHỚP)";
                $isValid = false;
            }

            if ($isStructureOnly) {
                echo " (structure only)";
            }

            echo "\n";

            return $isValid;

        } catch (Exception $e) {
            echo "   ❌ {$table}: Lỗi kiểm tra - " . $e->getMessage() . "\n";
            return false;
        }
    }

    // Thêm function để hiển thị summary lỗi
    public function showErrorSummary($errorTables) {
        if (empty($errorTables)) {
            return;
        }

        echo "\n📋 CHI TIẾT LỖI CÁC BẢNG:\n";
        echo "=" . str_repeat("=", 50) . "\n";

        foreach ($errorTables as $table) {
            echo "🔴 Bảng: {$table}\n";

            // Kiểm tra bảng có tồn tại trong source không
            try {
                $exists = DB::select("SHOW TABLES FROM `{$this->sourceDb}` LIKE '{$table}'");
                if (empty($exists)) {
                    echo "   ❌ Bảng không tồn tại trong source database\n";
                } else {
                    echo "   ✅ Bảng tồn tại trong source database\n";

                    // Kiểm tra số lượng bản ghi
                    $count = DB::select("SELECT COUNT(*) as count FROM `{$this->sourceDb}`.`{$table}`")[0]->count;
                    echo "   📊 Số bản ghi: " . number_format($count) . "\n";

                    // Kiểm tra cấu trúc bảng
                    try {
                        $structure = DB::select("SHOW CREATE TABLE `{$this->sourceDb}`.`{$table}`");
                        echo "   ✅ Cấu trúc bảng hợp lệ\n";
                    } catch (Exception $e) {
                        echo "   ❌ Lỗi cấu trúc bảng: " . $e->getMessage() . "\n";
                    }
                }
            } catch (Exception $e) {
                echo "   ❌ Lỗi kiểm tra: " . $e->getMessage() . "\n";
            }

            echo "\n";
        }
    }

    public function showDatabaseInfo() {
        echo "📊 THÔNG TIN DATABASE:\n";
        echo "=" . str_repeat("=", 40) . "\n";

        // Source database info
        echo "🗂️  Source: {$this->sourceDb}\n";
        try {
            $sourceTables = $this->getSourceTables();
            echo "   Số bảng sẽ copy: " . count($sourceTables) . "\n";

            $totalRecords = 0;
            foreach ($sourceTables as $table) {
                $count = DB::select("SELECT COUNT(*) as count FROM `{$this->sourceDb}`.`{$table}`")[0]->count;
                $totalRecords += $count;
            }
            echo "   Tổng bản ghi: " . number_format($totalRecords) . "\n";

        } catch (Exception $e) {
            echo "   ❌ Lỗi: " . $e->getMessage() . "\n";
        }

        // Target database info
        echo "\n🗂️  Target: {$this->targetDb}\n";
        try {
            $targetTables = $this->getTargetTables();
            echo "   Số bảng: " . count($targetTables) . "\n";

            if (!empty($targetTables)) {
                $totalRecords = 0;
                foreach ($targetTables as $table) {
                    try {
                        $count = DB::select("SELECT COUNT(*) as count FROM `{$this->targetDb}`.`{$table}`")[0]->count;
                        $totalRecords += $count;
                    } catch (Exception $e) {
                        // Bỏ qua lỗi nếu không thể đếm bản ghi
                    }
                }
                echo "   Tổng bản ghi: " . number_format($totalRecords) . "\n";
            }

        } catch (Exception $e) {
            echo "   Database chưa tồn tại hoặc rỗng\n";
        }

        echo "\n📋 Bảng chỉ copy cấu trúc:\n";
        foreach ($this->structureOnlyTables as $table) {
            echo "   - {$table}\n";
        }

        echo "\n🚫 Bảng bỏ qua hoàn toàn:\n";
        foreach ($this->skipTables as $table) {
            echo "   - {$table}\n";
        }
        echo "\n";
    }
}

// ========== MAIN EXECUTION ==========

try {
    $copier = new DatabaseCopier();

    // Hiển thị thông tin database
    $copier->showDatabaseInfo();
    $copier->deleteDbOld();

    echo "📝 CHỌN CHẾ ĐỘ:\n";
    echo "1. DRY RUN (preview only)\n";
    echo "2. EXECUTE COPY\n";
    echo "3. VALIDATE EXISTING COPY\n";
//
//    if (php_sapi_name() === 'cli') {
//        echo "Nhập lựa chọn (1-3): ";
//        $choice = trim(fgets(STDIN));
//    } else {
//        // Web mode - default to dry run
//        $choice = '1';
//        echo "🔍 Chạy DRY RUN (web mode)...\n\n";
//    }

    $choice = 2;
    switch ($choice) {
        case '1':
            echo "\n🔍 Chạy DRY RUN...\n";
            $copier->copyDatabase(true);
            break;

        case '2':
            if (php_sapi_name() === 'cli') {
                echo "\n⚠️  Bạn có chắc muốn EXECUTE copy? (yes/no): ";
//                $confirm = trim(fgets(STDIN));

//                if (strtolower($confirm) === 'yes')
                if(1)
                {
                    echo "\n🔧 Thực hiện copy...\n";
                    $copier->copyDatabase(false);
                } else {
                    echo "❌ Copy bị hủy\n";
                }
            } else {
                echo "❌ Execute mode chỉ khả dụng trong CLI\n";
            }
            break;

        case '3':
            echo "\n🔍 Kiểm tra copy hiện tại...\n";
            $copier->validateCopy();
            break;

        default:
            echo "❌ Lựa chọn không hợp lệ\n";
            break;
    }

} catch (Exception $e) {
    echo "❌ Lỗi fatal: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Script hoàn thành\n";

?>
