<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

/**
 * Flexible File ID Mapper
 *
 * Tìm trường $field trong $table
 * Nếu thấy thì tạo trường old_$field nếu chưa có, và backup giá trị ra table.field = table.old_field
 * Sau đó tách table.field ra các giá trị cách nhau bằng dấu phẩy (explode(',', $fieldValue))
 * giả sử thấy table.field = num1, num2,num3...
 * rồi tìm trong bảng $sourceTable.$oldIdField = num1 / num2 / num3
 * lấy ra $sourceTable.$newIdField tương ứng: newId1, newId2, newId3
 *
 * và cập nhật lại table.field = newId1,newId2,...
 */

function changeFileIdInTableFlexible($table, $field = 'image_list', $sourceTable = 'file_uploads', $oldIdField = 'old_id', $newIdField = 'id') {
    echo "🔄 Đang xử lý {$table}.{$field} (source: {$sourceTable}.{$oldIdField} -> {$newIdField})...\n";

    // Kiểm tra bảng target có tồn tại
    $tableExists = DB::select("SHOW TABLES LIKE '{$table}'");
    if (empty($tableExists)) {
        echo "❌ Bảng {$table} không tồn tại!\n";
        return false;
    }

    // Kiểm tra bảng source có tồn tại
    $sourceTableExists = DB::select("SHOW TABLES LIKE '{$sourceTable}'");
    if (empty($sourceTableExists)) {
        echo "❌ Bảng source {$sourceTable} không tồn tại!\n";
        return false;
    }

    // Kiểm tra trường có tồn tại
    $fieldExists = DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$field}'");
    if (empty($fieldExists)) {
        echo "❌ Trường {$field} không tồn tại trong bảng {$table}!\n";
        return false;
    }

    // Kiểm tra trường source
    $oldIdFieldExists = DB::select("SHOW COLUMNS FROM `{$sourceTable}` LIKE '{$oldIdField}'");
    if (empty($oldIdFieldExists)) {
        echo "❌ Trường {$oldIdField} không tồn tại trong bảng {$sourceTable}!\n";
        return false;
    }

    $newIdFieldExists = DB::select("SHOW COLUMNS FROM `{$sourceTable}` LIKE '{$newIdField}'");
    if (empty($newIdFieldExists)) {
        echo "❌ Trường {$newIdField} không tồn tại trong bảng {$sourceTable}!\n";
        return false;
    }

    // Kiểm tra và tạo trường old_$field nếu chưa có
    $oldFieldExists = DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'old_{$field}'");
    if (empty($oldFieldExists)) {
        // Lấy định nghĩa của trường gốc
        $fieldInfo = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = '{$field}'")[0];
        $fieldType = $fieldInfo->Type;
        $nullable = $fieldInfo->Null === 'YES' ? 'NULL' : 'NOT NULL';
        $defaultValue = $fieldInfo->Default ? "DEFAULT '{$fieldInfo->Default}'" : '';

        // Tạo trường old_$field
        $sql = "ALTER TABLE `{$table}` ADD COLUMN `old_{$field}` {$fieldType} {$nullable} {$defaultValue} AFTER `{$field}`";
        DB::statement($sql);
        echo "✅ Đã tạo trường old_{$field}\n";
    }

    // Lấy tất cả bản ghi có field không null và không rỗng
    $records = DB::table($table)
        ->select('id', $field)
        ->whereNotNull($field)
        ->where($field, '!=', '')
        ->get();

    echo "📋 Tìm thấy " . $records->count() . " bản ghi có {$field}\n";

    $updated = 0;
    $errors = 0;

    $cc = 0;
    $tt = $records->count();
    foreach ($records as $record) {
        $cc++;
        echo "\n🔍 $cc / $tt . Xử lý ID = {$record->id}\n";

        try {
            $fieldValue = $record->$field;
            $recordId = $record->id;

            // Tách các ID cũ bằng dấu phẩy
            $oldIds = array_map('trim', explode(',', $fieldValue));
            $oldIds = array_filter($oldIds); // Loại bỏ giá trị rỗng

            if (empty($oldIds)) {
                echo "⚠️ Không có ID nào để xử lý\n";
                continue;
            }

            echo "   🔎 Tìm mapping cho: " . implode(',', $oldIds) . "\n";

            $newIds = [];
            $notFoundIds = [];

            foreach ($oldIds as $oldId) {
                // Tìm trong source table
                $sourceRecord = DB::table($sourceTable)
                    ->select($newIdField)
                    ->where($oldIdField, $oldId)
                    ->first();

                if ($sourceRecord) {
                    $newId = $sourceRecord->$newIdField;
                    $newIds[] = $newId;
                    echo "   ✅ {$oldId} -> {$newId}\n";
                } else {
                    $notFoundIds[] = $oldId;
                    echo "   ❌ Không tìm thấy {$oldIdField} = {$oldId} trong {$sourceTable}\n";
                }
            }

            if (!empty($newIds)) {
                // Cập nhật field với các ID mới
                $newFieldValue = implode(',', $newIds);

                // Backup giá trị cũ vào old_field trước
                DB::table($table)
                    ->where('id', $recordId)
                    ->update(["old_{$field}" => $fieldValue]);

                echo "   💾 Backup: old_{$field} = '{$fieldValue}'\n";

                // Cập nhật field với giá trị mới
                DB::table($table)
                    ->where('id', $recordId)
                    ->update([$field => $newFieldValue]);

                echo "   ✅ Cập nhật: {$field} = '{$newFieldValue}'\n";

                if (!empty($notFoundIds)) {
                    echo "   ⚠️ Không tìm thấy: " . implode(',', $notFoundIds) . "\n";
                }

                $updated++;
            } else {
                echo "   ❌ Không tìm thấy mapping nào cho {$fieldValue}\n";
                $errors++;
            }

        } catch (Exception $e) {
            echo "   ❌ Lỗi xử lý {$table}[{$record->id}]: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    echo "\n🎉 Hoàn thành {$table}.{$field}: Cập nhật {$updated}, Lỗi {$errors}\n\n";

    return [
        'total' => $records->count(),
        'updated' => $updated,
        'errors' => $errors
    ];
}

// ========== HELPER FUNCTION FOR BATCH PROCESSING ==========

function batchChangeFileIds($configs) {
    echo "🚀 Bắt đầu batch processing...\n\n";

    $totalUpdated = 0;
    $totalErrors = 0;

    foreach ($configs as $config) {
        $table = $config['table'];
        $field = $config['field'] ?? 'image_list';
        $sourceTable = $config['source_table'] ?? 'file_uploads';
        $oldIdField = $config['old_id_field'] ?? 'old_id';
        $newIdField = $config['new_id_field'] ?? 'id';

        $result = changeFileIdInTableFlexible($table, $field, $sourceTable, $oldIdField, $newIdField);

        if (is_array($result)) {
            $totalUpdated += $result['updated'];
            $totalErrors += $result['errors'];
        }
    }

    echo "📊 TỔNG KẾT BATCH:\n";
    echo "   Tổng cập nhật: {$totalUpdated}\n";
    echo "   Tổng lỗi: {$totalErrors}\n";
}

// ========== USAGE EXAMPLES ==========

// Ví dụ 1: Mapping từ file_uploads (mặc định)
// changeFileIdInTableFlexible('products', 'image_list', 'file_uploads', 'old_id', 'id');

// Ví dụ 2: Mapping từ bảng khác
// changeFileIdInTableFlexible('news', 'gallery_images', 'file_clouds', 'old_id', 'id');

// Ví dụ 3: Mapping từ users
// changeFileIdInTableFlexible('orders', 'user_ids', 'users', 'old_id', 'id');

// Ví dụ 4: Mapping custom fields
// changeFileIdInTableFlexible('products', 'thumbnail_ids', 'media_files', 'legacy_id', 'new_id');

// ========== BATCH CONFIGURATIONS ==========

$batchConfigs = [
    // Mapping file IDs from file_uploads
    [
        'table' => 'products',
        'field' => 'image_list',
        'source_table' => 'file_uploads',
        'old_id_field' => 'old_id',
        'new_id_field' => 'id'
    ],
    [
        'table' => 'news',
        'field' => 'gallery_images',
        'source_table' => 'file_uploads',
        'old_id_field' => 'old_id',
        'new_id_field' => 'id'
    ],
    // Mapping user IDs from users
    [
        'table' => 'orders',
        'field' => 'assigned_users',
        'source_table' => 'users',
        'old_id_field' => 'old_id',
        'new_id_field' => 'id'
    ],
    // Mapping from file_clouds
    [
        'table' => 'posts',
        'field' => 'attachments',
        'source_table' => 'file_clouds',
        'old_id_field' => 'old_id',
        'new_id_field' => 'id'
    ]
];

echo "📝 Cấu hình batch sẵn sàng:\n";
foreach ($batchConfigs as $i => $config) {
    echo "  " . ($i + 1) . ". {$config['table']}.{$config['field']} <- {$config['source_table']}.{$config['old_id_field']} -> {$config['new_id_field']}\n";
}
echo "\n";

// ========== CHẠY SINGLE OR BATCH ==========

// Chạy single
//return;
 changeFileIdInTableFlexible("news", "image_list", "file_uploads", "old_id", "id");
 changeFileIdInTableFlexible("gia_phas", "image_list", "file_uploads", "old_id", "id");
 changeFileIdInTableFlexible("my_tree_infos", "image_list", "file_uploads", "old_id", "id");

 changeFileIdInTableFlexible("gia_phas", "parent_id", "gia_phas", "old_id", "id");
 changeFileIdInTableFlexible("gia_phas", "married_with", "gia_phas", "old_id", "id");
 changeFileIdInTableFlexible("gia_phas", "child_of_second_married", "gia_phas", "old_id", "id");
 changeFileIdInTableFlexible("my_tree_infos", "tree_id", "gia_phas", "old_id", "id");

changeFileIdInTableFlexible("order_items", "order_id", "order_infos", "old_id", "id");


// Chạy batch (uncomment để chạy)
// batchChangeFileIds($batchConfigs);

echo "📝 Hướng dẫn sử dụng:\n";
echo "Function: changeFileIdInTableFlexible(\$table, \$field, \$sourceTable, \$oldIdField, \$newIdField)\n";
echo "- table: Bảng cần cập nhật\n";
echo "- field: Trường chứa danh sách ID cách nhau bằng dấu phẩy\n";
echo "- sourceTable: Bảng nguồn để mapping ID\n";
echo "- oldIdField: Trường chứa ID cũ trong bảng nguồn\n";
echo "- newIdField: Trường chứa ID mới trong bảng nguồn\n\n";

echo "Ví dụ:\n";
echo "changeFileIdInTableFlexible('products', 'image_list', 'file_uploads', 'old_id', 'id');\n";
echo "changeFileIdInTableFlexible('orders', 'user_ids', 'users', 'legacy_id', 'new_id');\n";

?>
