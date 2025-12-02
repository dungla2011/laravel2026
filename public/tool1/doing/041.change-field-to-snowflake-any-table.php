<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

// ========== CHANGE ANY TABLE ID TO SNOWFLAKE MIGRATOR ==========

function updateTableIdToSnowflake($tableName, $idField = 'id', $displayField = null) {
    echo "🔄 Bắt đầu cập nhật {$tableName}.{$idField} thành SnowFlake...\n";

    // Kiểm tra table có tồn tại không
    $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
    if (empty($tableExists)) {
        echo "❌ Bảng {$tableName} không tồn tại!\n";
        return false;
    }

    // Kiểm tra field có tồn tại không
    $fieldExists = DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE '{$idField}'");
    if (empty($fieldExists)) {
        echo "❌ Trường {$idField} không tồn tại trong bảng {$tableName}!\n";
        return false;
    }

    // Tắt foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    // Lấy tất cả records
    $query = DB::table($tableName)->select($idField);

    // Thêm displayField nếu có
    if ($displayField && DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE '{$displayField}'")) {
        $query->addSelect($displayField);
    }

    $records = $query->get();

    echo "📋 Tìm thấy " . $records->count() . " bản ghi trong bảng {$tableName}\n";

    if ($records->count() == 0) {
        echo "⚠️ Không có bản ghi nào để cập nhật\n";
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        return true;
    }

    $count = 0;
    $errors = 0;

    foreach ($records as $record) {
        try {
            usleep(500); // Delay để tránh conflict

            $oldId = $record->$idField;

            if($oldId > 1000000000000){
                echo "⚠️ {$tableName}[{$record->id}]: ID cũ {$oldId} đã update ID bỏ qua?\n";
                continue;
            }

            $newId = \GlxSnowflake::id();

            //Update trường old_$idField
            DB::table($tableName)->where($idField, $oldId)->update(["old_{$idField}" => $oldId]);

            // Update ID
            DB::table($tableName)->where($idField, $oldId)->update([$idField => $newId]);

            // Display info
            $displayInfo = '';
            if ($displayField && isset($record->$displayField)) {
                $displayInfo = " ({$record->$displayField})";
            }

            echo "✅ Updated: {$oldId} -> {$newId}{$displayInfo}\n";
            $count++;

        } catch (Exception $e) {
            echo "❌ Lỗi cập nhật {$oldId}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    // Bật lại foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    echo "🎉 Hoàn thành! Đã cập nhật {$count} bản ghi";
    if ($errors > 0) {
        echo " (Lỗi: {$errors})";
    }
    echo "\n\n";

    return ['success' => $count, 'errors' => $errors];
}

// ========== HELPER FUNCTIONS ==========

function showTableInfo($tableName) {
    echo "📊 Thông tin bảng {$tableName}:\n";

    // Đếm số bản ghi
    $count = DB::table($tableName)->count();
    echo "   Số bản ghi: {$count}\n";

    // Hiển thị cấu trúc bảng
    $columns = DB::select("SHOW COLUMNS FROM `{$tableName}`");
    echo "   Các trường:\n";
    foreach ($columns as $col) {
        $key = $col->Key ? " ({$col->Key})" : '';
        echo "     - {$col->Field}: {$col->Type}{$key}\n";
    }
    echo "\n";
}

function migrateMultipleTables($tables) {
    echo "🚀 Bắt đầu migrate nhiều bảng...\n\n";

    $totalSuccess = 0;
    $totalErrors = 0;

    foreach ($tables as $config) {
        $tableName = $config['table'];
        $idField = $config['id_field'] ?? 'id';
        $displayField = $config['display_field'] ?? null;

        $result = updateTableIdToSnowflake($tableName, $idField, $displayField);

        if (is_array($result)) {
            $totalSuccess += $result['success'];
            $totalErrors += $result['errors'];
        }
    }

    echo "📊 TỔNG KẾT:\n";
    echo "   Tổng cập nhật thành công: {$totalSuccess}\n";
    echo "   Tổng lỗi: {$totalErrors}\n";
}

// ========== USAGE EXAMPLES ==========

// Ví dụ 1: Cập nhật bảng users

updateTableIdToSnowflake('file_uploads', 'id', 'name');
updateTableIdToSnowflake('gia_phas', 'id', 'name');
updateTableIdToSnowflake('my_tree_infos');
updateTableIdToSnowflake('order_infos');
updateTableIdToSnowflake('order_items');

// Ví dụ 2: Cập nhật bảng products
// updateTableIdToSnowflake('products', 'id', 'name');

// Ví dụ 3: Cập nhật bảng orders
// updateTableIdToSnowflake('orders', 'id', 'order_number');

// Ví dụ 4: Cập nhật nhiều bảng cùng lúc
/*
$tablesToMigrate = [
    ['table' => 'users', 'id_field' => 'id', 'display_field' => 'email'],
    ['table' => 'products', 'id_field' => 'id', 'display_field' => 'name'],
    ['table' => 'orders', 'id_field' => 'id', 'display_field' => 'order_number'],
    ['table' => 'categories', 'id_field' => 'id', 'display_field' => 'title'],
];

migrateMultipleTables($tablesToMigrate);
*/

// ========== CHẠY MIGRATION ==========

// Uncomment dòng dưới để chạy cho bảng cụ thể
// updateTableIdToSnowflake('users', 'id', 'email');

// Hoặc xem thông tin bảng trước
// showTableInfo('users');

echo "📝 Hướng dẫn sử dụng:\n";
echo "1. Uncomment dòng updateTableIdToSnowflake() để chạy\n";
echo "2. Tham số: updateTableIdToSnowflake(table, id_field, display_field)\n";
echo "   - table: tên bảng\n";
echo "   - id_field: tên trường ID (mặc định 'id')\n";
echo "   - display_field: trường hiển thị thông tin (optional)\n\n";

echo "Ví dụ:\n";
echo "updateTableIdToSnowflake('users', 'id', 'email');\n";
echo "updateTableIdToSnowflake('products', 'product_id', 'name');\n";
echo "updateTableIdToSnowflake('orders');\n";

?>
