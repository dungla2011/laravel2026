<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';



/**Tìm trường $field trong $table
Nếu thấy thì tạo trường old_$field nếu chưa có, và backup giá trị ra table.field = table.old_field
Sau đó tách table.field ra các giá trị cách nhau bời giấu phẩy (explode(',', $fieldValue))
giả sử thấy table.field = num1, num2,num3...
rồi tìm trong bảng file_uploads.old_id = num1 / num2 / num3
lấy ra file_uploads.id tương ứng: file_uploads.id1, file_uploads.id2, file_uploads.id3

và cập nhật lại table.field = file_uploads.id1,file_uploads.id2,...


Ví dụ: products.image_list = "123,456,789"

1. Explode: [123, 456, 789]
2. Tìm file_uploads:
   - old_id=123 -> id=999
   - old_id=456 -> id=888
   - old_id=789 -> id=777
3. Cập nhật: products.image_list = "999,888,777"
4. Backup: products.old_image_list = "123,456,789"


*/

function changeFileIdInTable($table, $field = 'image_list') {
    echo "🔄 Đang xử lý {$table}.{$field}...\n";

    // Kiểm tra bảng có tồn tại
    $tableExists = DB::select("SHOW TABLES LIKE '{$table}'");
    if (empty($tableExists)) {
        echo "❌ Bảng {$table} không tồn tại!\n";
        return false;
    }

    // Kiểm tra trường có tồn tại
    $fieldExists = DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$field}'");
    if (empty($fieldExists)) {
        echo "❌ Trường {$field} không tồn tại trong bảng {$table}!\n";
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

    // Backup dữ liệu: old_field = field (nếu old_field còn null)
    // DB::statement("UPDATE `{$table}` SET `old_{$field}` = `{$field}` WHERE `old_{$field}` IS NULL");
    // echo "✅ Đã backup dữ liệu vào old_{$field}\n";

    // Lấy tất cả bản ghi có field không null và không rỗng
    $records = DB::table($table)
        ->select('id', $field)
        ->whereNotNull($field)
        ->where($field, '!=', '')
        ->get();

    echo "📋 Tìm thấy " . $records->count() . " bản ghi có {$field}\n";

    $updated = 0;
    $errors = 0;

    foreach ($records as $record) {


        echo "\n ID = $record->id\n";

        try {
            $fieldValue = $record->$field;
            $recordId = $record->id;

            // Tách các ID cũ bằng dấu phẩy
            $oldIds = array_map('trim', explode(',', $fieldValue));
            $oldIds = array_filter($oldIds); // Loại bỏ giá trị rỗng

            if (empty($oldIds)) {
                continue;
            }

            $newIds = [];
            $notFoundIds = [];

            foreach ($oldIds as $oldId) {
                // Tìm file_uploads có old_id tương ứng
                $fileUpload = DB::table('file_uploads')
                    ->select('id')
                    ->where('old_id', $oldId)
                    ->first();

                if ($fileUpload) {
                    $newIds[] = $fileUpload->id;
                } else {
                    $notFoundIds[] = $oldId;
                }
            }

            if (!empty($newIds)) {
                // Cập nhật field với các ID mới
                $newFieldValue = implode(',', $newIds);

                //Bây giờ mới update old_field
                // Không lo bị update nhầm nếu chạy lại ,vì khi chạy lại fieldValue đã được thay đổi rồi thì ko tìm thấy id bên cũ nữa.
                DB::table($table)
                    ->where('id', $recordId)
                    ->update(["old_{$field}" => $fieldValue]);

                echo "✅ {$table}[{$recordId}]: {$fieldValue} -> {$newFieldValue}";

                // getch("...");
                DB::table($table)
                    ->where('id', $recordId)
                    ->update([$field => $newFieldValue]);

                echo "✅ {$table}[{$recordId}]: {$fieldValue} -> {$newFieldValue}";

                // getch("...");
                if (!empty($notFoundIds)) {
                    echo " (Không tìm thấy: " . implode(',', $notFoundIds) . ")";
                }
                echo "\n";

                $updated++;
            } else {
                echo "❌ {$table}[{$recordId}]: Không tìm thấy file_uploads nào cho {$fieldValue}\n";
                $errors++;
            }

        } catch (Exception $e) {
            echo "❌ Lỗi xử lý {$table}[{$record->id}]: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    echo "🎉 Hoàn thành {$table}.{$field}: Cập nhật {$updated}, Lỗi {$errors}\n\n";

    return [
        'total' => $records->count(),
        'updated' => $updated,
        'errors' => $errors
    ];
}

// ========== USAGE EXAMPLES ==========

// Ví dụ sử dụng
// changeFileIdInTable('products', 'image_list');
// changeFileIdInTable('news', 'gallery_images');
// changeFileIdInTable('posts', 'attachments');


// Test với nhiều bảng
$tables = [
    ['table' => 'products', 'field' => 'image_list'],
    ['table' => 'news', 'field' => 'gallery_images'],
    ['table' => 'posts', 'field' => 'attachments'],
];

echo "📝 Danh sách bảng sẽ xử lý:\n";
foreach ($tables as $config) {
    echo "  - {$config['table']}.{$config['field']}\n";
}
echo "\n";

// Uncomment để chạy
/*
foreach ($tables as $config) {
    changeFileIdInTable($config['table'], $config['field']);
}
*/

// changeFileIdInTable("gia_phas");
changeFileIdInTable("news");

// echo "📝 Hướng dẫn:\n";
// echo "1. Uncomment các dòng trên để chạy\n";
// echo "2. Function sẽ:\n";
// echo "   - Tạo trường old_field để backup\n";
// echo "   - Tách field bằng dấu phẩy\n";
// echo "   - Map old_id -> new_id từ file_uploads\n";
// echo "   - Cập nhật field với new_id\n\n";

// echo "Ví dụ:\n";
// echo "Trước: products.image_list = '123,456,789'\n";
// echo "Sau:   products.image_list = '999,888,777'\n";
// echo "       products.old_image_list = '123,456,789'\n";
