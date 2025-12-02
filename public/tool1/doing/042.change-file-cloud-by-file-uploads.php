<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

// ========== UPDATE FILE_CLOUDS ID FROM FILE_UPLOADS ==========

// DB::statement("UPDATE file_uploads SET cloud_id = id");

function updateFileCloudsFromFileUploads() {
    echo "🔄 Bắt đầu cập nhật file_clouds.id từ file_uploads...\n";

    // Kiểm tra các bảng có tồn tại không
    $tables = ['file_clouds', 'file_uploads'];
    foreach ($tables as $table) {
        $exists = DB::select("SHOW TABLES LIKE '{$table}'");
        if (empty($exists)) {
            echo "❌ Bảng {$table} không tồn tại!\n";
            return false;
        }
    }

    // Kiểm tra các trường cần thiết
    $requiredFields = [
        'file_clouds' => ['id', 'old_id'],
        'file_uploads' => ['id', 'old_id']
    ];

    foreach ($requiredFields as $table => $fields) {
        foreach ($fields as $field) {
            $exists = DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$field}'");
            if (empty($exists)) {
                echo "❌ Trường {$field} không tồn tại trong bảng {$table}!\n";
                return false;
            }
        }
    }

    // Tắt foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    // Lấy tất cả file_clouds có old_id
    $fileClouds = DB::table('file_clouds')
        ->select('id', 'old_id', 'name')
        ->whereNotNull('old_id')
        ->get();

    echo "📋 Tìm thấy " . $fileClouds->count() . " bản ghi file_clouds có old_id\n";

    if ($fileClouds->count() == 0) {
        echo "⚠️ Không có bản ghi nào để cập nhật\n";
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        return true;
    }

    $updated = 0;
    $notFound = 0;
    $errors = 0;

    foreach ($fileClouds as $fileCloud) {
        try {
            $oldId = $fileCloud->old_id;
            $currentId = $fileCloud->id;

            // Tìm file_uploads có cùng old_id
            $fileUpload = DB::table('file_uploads')
                ->select('id', 'name')
                ->where('old_id', $oldId)
                ->first();

            if ($fileUpload) {
                $newId = $fileUpload->id;

                // Cập nhật file_clouds.id
                DB::table('file_clouds')
                    ->where('id', $currentId)
                    ->update(['id' => $newId]);

                $filename = $fileCloud->name ?? 'N/A';
                echo "✅ Updated: file_clouds[{$currentId}] -> [{$newId}] (old_id: {$oldId}, file: {$filename})\n";
                $updated++;

            } else {
                $filename = $fileCloud->name ?? 'N/A';
                echo "❌ Không tìm thấy file_uploads có old_id = {$oldId} cho file_clouds[{$currentId}] ({$filename})\n";
                $notFound++;
            }

        } catch (Exception $e) {
            echo "❌ Lỗi cập nhật file_clouds[{$fileCloud->id}]: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    // Bật lại foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    echo "\n📊 KẾT QUẢ:\n";
    echo "   Cập nhật thành công: {$updated}\n";
    echo "   Không tìm thấy: {$notFound}\n";
    echo "   Lỗi: {$errors}\n";
    echo "   Tổng xử lý: " . $fileClouds->count() . "\n\n";

    return [
        'total' => $fileClouds->count(),
        'updated' => $updated,
        'not_found' => $notFound,
        'errors' => $errors
    ];
}

// ========== HELPER FUNCTIONS ==========

function showFileTables() {
    echo "📊 Thông tin các bảng file:\n\n";

    $tables = ['file_clouds', 'file_uploads'];

    foreach ($tables as $table) {
        echo "🗂️  Bảng {$table}:\n";

        try {
            // Đếm tổng số bản ghi
            $total = DB::table($table)->count();
            echo "   Tổng bản ghi: {$total}\n";

            // Đếm số bản ghi có old_id
            $hasOldId = DB::table($table)->whereNotNull('old_id')->count();
            echo "   Có old_id: {$hasOldId}\n";

            // Hiển thị một vài bản ghi mẫu
            $samples = DB::table($table)
                ->select('id', 'old_id', 'filename')
                ->limit(3)
                ->get();

            if ($samples->count() > 0) {
                echo "   Mẫu dữ liệu:\n";
                foreach ($samples as $sample) {
                    $filename = $sample->name ?? 'N/A';
                    echo "     - ID: {$sample->id}, old_id: {$sample->old_id}, file: {$filename}\n";
                }
            }

        } catch (Exception $e) {
            echo "   ❌ Lỗi: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }
}

function validateFileRelation() {
    echo "🔍 Kiểm tra mối quan hệ giữa file_clouds và file_uploads:\n";

    // Lấy danh sách old_id từ file_clouds
    $cloudOldIds = DB::table('file_clouds')
        ->whereNotNull('old_id')
        ->pluck('old_id')
        ->toArray();

    // Lấy danh sách old_id từ file_uploads
    $uploadOldIds = DB::table('file_uploads')
        ->whereNotNull('old_id')
        ->pluck('old_id')
        ->toArray();

    $matches = array_intersect($cloudOldIds, $uploadOldIds);
    $cloudsOnly = array_diff($cloudOldIds, $uploadOldIds);
    $uploadsOnly = array_diff($uploadOldIds, $cloudOldIds);

    echo "📊 Thống kê:\n";
    echo "   file_clouds có old_id: " . count($cloudOldIds) . "\n";
    echo "   file_uploads có old_id: " . count($uploadOldIds) . "\n";
    echo "   Trùng khớp: " . count($matches) . "\n";
    echo "   Chỉ có trong file_clouds: " . count($cloudsOnly) . "\n";
    echo "   Chỉ có trong file_uploads: " . count($uploadsOnly) . "\n\n";

    if (!empty($cloudsOnly)) {
        echo "⚠️  old_id chỉ có trong file_clouds (sẽ không update được):\n";
        foreach (array_slice($cloudsOnly, 0, 10) as $oldId) {
            echo "   - {$oldId}\n";
        }
        if (count($cloudsOnly) > 10) {
            echo "   ... và " . (count($cloudsOnly) - 10) . " old_id khác\n";
        }
        echo "\n";
    }
}

// ========== USAGE ==========

// Hiển thị thông tin bảng trước khi chạy
showFileTables();

// Kiểm tra mối quan hệ
validateFileRelation();

// Uncomment để chạy migration
updateFileCloudsFromFileUploads();

echo "📝 Hướng dẫn sử dụng:\n";
echo "1. Kiểm tra thông tin bảng ở trên\n";
echo "2. Uncomment dòng updateFileCloudsFromFileUploads() để chạy\n";
echo "3. Script sẽ:\n";
echo "   - Duyệt qua tất cả file_clouds có old_id\n";
echo "   - Tìm file_uploads có cùng old_id\n";
echo "   - Cập nhật file_clouds.id = file_uploads.id\n\n";

echo "⚠️  Lưu ý: Script sẽ thay đổi ID trong file_clouds!\n";
echo "Backup database trước khi chạy!\n";

?>
