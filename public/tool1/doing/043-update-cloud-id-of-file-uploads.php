<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

//  Update sau đó vào lại https://v5.mytree.vn/member/file xem ảnh có lên không

function updateFileUploadsCloudId() {
    echo "🔄 Cập nhật file_uploads.cloud_id...\n";

    // Lấy tất cả file_uploads có cloud_id
    $fileUploads = DB::table('file_uploads')
        ->select('id', 'cloud_id', 'name')
        ->whereNotNull('cloud_id')
        ->get();

    echo "📋 Tìm thấy " . $fileUploads->count() . " file_uploads có cloud_id\n";

    $updated = 0;
    $notFound = 0;

    foreach ($fileUploads as $fileUpload) {
        $oldCloudId = $fileUpload->cloud_id;

        // Tìm file_clouds có old_id = cloud_id
        $fileCloud = DB::table('file_clouds')
            ->select('id')
            ->where('old_id', $oldCloudId)
            ->first();

        if ($fileCloud) {
            $newCloudId = $fileCloud->id;

            // Cập nhật cloud_id mới
            DB::table('file_uploads')
                ->where('id', $fileUpload->id)
                ->update(['cloud_id' => $newCloudId]);

            echo "✅ {$fileUpload->id}: cloud_id {$oldCloudId} -> {$newCloudId} ({$fileUpload->name})\n";
            $updated++;
        } else {
            echo "❌ Không tìm thấy file_clouds.old_id = {$oldCloudId} cho file_uploads[{$fileUpload->id}]\n";
            $notFound++;
        }
    }

    echo "🎉 Hoàn thành: Cập nhật {$updated}, Không tìm thấy {$notFound}\n";
}

// Chạy function
updateFileUploadsCloudId();

?>
