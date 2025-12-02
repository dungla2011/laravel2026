<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

// ========== DROP FOREIGN KEY CONSTRAINTS ==========

function dropForeignKeyConstraints() {
    echo "🔧 Bắt đầu xoá foreign key constraints...\n";
    echo "=" . str_repeat("=", 50) . "\n";

    // Danh sách các lệnh DROP FOREIGN KEY
    $dropConstraints = [
        [
            'table' => 'skus_product_variant_options',
            'constraint' => 'spvo_product_variant_id_product_variants_id'
        ],
        [
            'table' => 'skus_product_variant_options',
            'constraint' => 'skus_product_variant_options_sku_id_skus_id'
        ],
        [
            'table' => 'product_variants',
            'constraint' => 'product_variants_product_id_products_id'
        ],
        [
            'table' => 'product_variant_options',
            'constraint' => 'product_variant_options_product_variant_id_product_variants_id'
        ],
        [
            'table' => 'skus_product_variant_options',
            'constraint' => 'spvo_product_variant_options_id_product_variant_options_id'
        ],
        [
            'table' => 'skus',
            'constraint' => 'skus_product_id_products_id'
        ]
    ];

    $successCount = 0;
    $errorCount = 0;
    $errorDetails = [];

    // Duyệt qua từng constraint
    foreach ($dropConstraints as $index => $constraint) {
        $table = $constraint['table'];
        $constraintName = $constraint['constraint'];
        $sql = "ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraintName}`";

        echo "🔄 [" . ($index + 1) . "/" . count($dropConstraints) . "] Xoá constraint: {$table}.{$constraintName}... ";

        try {
            // Kiểm tra bảng có tồn tại không
            $tableExists = DB::select("SHOW TABLES LIKE '{$table}'");
            if (empty($tableExists)) {
                echo "⚠️  Bảng không tồn tại\n";
                continue;
            }

            // Kiểm tra constraint có tồn tại không
            $constraintExists = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.table_constraints
                WHERE table_schema = DATABASE()
                AND table_name = '{$table}'
                AND constraint_name = '{$constraintName}'
                AND constraint_type = 'FOREIGN KEY'
            ");

            if (empty($constraintExists)) {
                echo "⚠️  Constraint không tồn tại\n";
                continue;
            }

            // Thực hiện DROP FOREIGN KEY
            DB::statement($sql);
            echo "✅ Thành công\n";
            $successCount++;

        } catch (Exception $e) {
            echo "❌ Lỗi\n";
            $errorMessage = $e->getMessage();
            $errorDetails[] = [
                'table' => $table,
                'constraint' => $constraintName,
                'sql' => $sql,
                'error' => $errorMessage
            ];
            $errorCount++;

            echo "   🔴 Chi tiết: {$errorMessage}\n";
        }
    }

    // Tổng kết
    echo "\n📊 KẾT QUẢ:\n";
    echo "=" . str_repeat("=", 30) . "\n";
    echo "✅ Thành công: {$successCount} constraints\n";
    echo "❌ Lỗi: {$errorCount} constraints\n";
    echo "📋 Tổng cộng: " . count($dropConstraints) . " constraints\n";

    // Hiển thị chi tiết lỗi
    if (!empty($errorDetails)) {
        echo "\n🔴 CHI TIẾT CÁC LỖI:\n";
        echo "-" . str_repeat("-", 60) . "\n";

        foreach ($errorDetails as $i => $error) {
            echo ($i + 1) . ". Bảng: {$error['table']}\n";
            echo "   Constraint: {$error['constraint']}\n";
            echo "   SQL: {$error['sql']}\n";
            echo "   Lỗi: {$error['error']}\n\n";
        }
    }

    return [
        'success' => $successCount,
        'errors' => $errorCount,
        'total' => count($dropConstraints),
        'error_details' => $errorDetails
    ];
}

// ========== HELPER FUNCTIONS ==========

function showCurrentConstraints() {
    echo "📋 FOREIGN KEY CONSTRAINTS HIỆN TẠI:\n";
    echo "=" . str_repeat("=", 40) . "\n";

    $tables = [
        'skus_product_variant_options',
        'product_variants',
        'product_variant_options',
        'skus'
    ];

    foreach ($tables as $table) {
        echo "\n🗂️  Bảng: {$table}\n";

        try {
            $constraints = DB::select("
                SELECT
                    CONSTRAINT_NAME,
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM information_schema.key_column_usage
                WHERE table_schema = DATABASE()
                AND table_name = '{$table}'
                AND referenced_table_name IS NOT NULL
                ORDER BY CONSTRAINT_NAME
            ");

            if (empty($constraints)) {
                echo "   ℹ️  Không có foreign key constraints\n";
            } else {
                foreach ($constraints as $constraint) {
                    echo "   - {$constraint->CONSTRAINT_NAME}\n";
                    echo "     {$constraint->COLUMN_NAME} -> {$constraint->REFERENCED_TABLE_NAME}.{$constraint->REFERENCED_COLUMN_NAME}\n";
                }
            }

        } catch (Exception $e) {
            echo "   ❌ Lỗi: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

function validateConstraintsDrop() {
    echo "🔍 KIỂM TRA SAU KHI XOÁ CONSTRAINTS:\n";
    echo "=" . str_repeat("=", 40) . "\n";

    $constraintsToCheck = [
        'spvo_product_variant_id_product_variants_id',
        'skus_product_variant_options_sku_id_skus_id',
        'product_variants_product_id_products_id',
        'product_variant_options_product_variant_id_product_variants_id',
        'spvo_product_variant_options_id_product_variant_options_id',
        'skus_product_id_products_id'
    ];

    $stillExists = [];

    foreach ($constraintsToCheck as $constraintName) {
        $exists = DB::select("
            SELECT table_name, constraint_name
            FROM information_schema.table_constraints
            WHERE table_schema = DATABASE()
            AND constraint_name = '{$constraintName}'
            AND constraint_type = 'FOREIGN KEY'
        ");

        if (!empty($exists)) {
            $stillExists[] = $constraintName;
            echo "⚠️  Vẫn tồn tại: {$constraintName} trong bảng {$exists[0]->table_name}\n";
        } else {
            echo "✅ Đã xoá: {$constraintName}\n";
        }
    }

    if (empty($stillExists)) {
        echo "\n🎉 Tất cả constraints đã được xoá thành công!\n";
    } else {
        echo "\n⚠️  Còn " . count($stillExists) . " constraints chưa được xoá\n";
    }

    return $stillExists;
}

// ========== MAIN EXECUTION ==========



// ========== SIMPLE ALTER TABLE RUNNER ==========

function runAlterTableCommands() {
    echo "🔧 Bắt đầu chạy các lệnh ALTER TABLE...\n";
    echo "=" . str_repeat("=", 50) . "\n";

    // Danh sách các lệnh ALTER TABLE
    $alterCommands = [
        "UPDATE `roles` SET `id` = '0' WHERE `roles`.`id` = 7",
        "ALTER TABLE `assets` CHANGE `purchase_date` `purchase_date` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `event_infos` CHANGE `time_start_check_in` `time_start_check_in` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `event_send_actions` CHANGE `last_force_send` `last_force_send` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `event_send_actions` CHANGE `pushed_all_sms_to_queue` `pushed_all_sms_to_queue` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `event_send_info_logs` CHANGE `last_app_sms_request_to_send` `last_app_sms_request_to_send` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `file_clouds` CHANGE `last_save_doc` `last_save_doc` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `hateco_certificates` CHANGE `ngay_sinh` `ngay_sinh` TIMESTAMP NULL DEFAULT NULL",
        "ALTER TABLE `order_items` CHANGE `end_time` `end_time` TIMESTAMP NULL DEFAULT NULL"
    ];

    $successCount = 0;
    $errorCount = 0;

    foreach ($alterCommands as $index => $sql) {
        $commandNumber = $index + 1;
        $totalCommands = count($alterCommands);

        echo "🔄 [{$commandNumber}/{$totalCommands}] Chạy lệnh... ";

        try {
            DB::statement($sql);
            echo "✅ Thành công\n";
            $successCount++;

        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }

    echo "\n📊 KẾT QUẢ:\n";
    echo "✅ Thành công: {$successCount} lệnh\n";
    echo "❌ Lỗi: {$errorCount} lệnh\n";
    echo "📋 Tổng cộng: " . count($alterCommands) . " lệnh\n";

    if ($errorCount === 0) {
        echo "\n🎉 Tất cả lệnh đã chạy thành công!\n";
    }
}

// ========== CHẠY LỆNH ==========

try {
    runAlterTableCommands();
} catch (Exception $e) {
    echo "❌ Lỗi fatal: " . $e->getMessage() . "\n";
    exit();
}

try {
    echo "🚀 DROP FOREIGN KEY CONSTRAINTS TOOL\n";
    echo "=" . str_repeat("=", 50) . "\n\n";

    // Hiển thị constraints hiện tại
    showCurrentConstraints();

    echo "📝 CHỌN HÀNH ĐỘNG:\n";
    echo "1. Hiển thị constraints hiện tại\n";
    echo "2. Thực hiện DROP constraints\n";
    echo "3. Kiểm tra sau khi drop\n";

//    if (php_sapi_name() === 'cli') {
//        echo "Nhập lựa chọn (1-3): ";
//        $choice = trim(fgets(STDIN));
//    } else {
//        // Web mode - default to execute
//        $choice = '2';
//        echo "🔧 Chạy DROP constraints (web mode)...\n\n";
//    }

    $choice = 2;
    switch ($choice) {
        case '1':
            showCurrentConstraints();
            break;

        case '2':
            $result = dropForeignKeyConstraints();

            if ($result['errors'] === 0) {
                echo "\n🎉 Tất cả constraints đã được xoá thành công!\n";
            } else {
                echo "\n⚠️  Có một số lỗi xảy ra, kiểm tra chi tiết ở trên\n";
            }
            break;

        case '3':
            $stillExists = validateConstraintsDrop();
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
