<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

function changeFieldUidOfTable($table, $uidField = 'user_id'){
    echo "🔄 Bắt đầu cập nhật {$table}.{$uidField}...\n";

    // Lấy tất cả bản ghi có $uidField không null
    $records = DB::table($table)
        ->select('id', $uidField)
        ->whereNotNull($uidField)
        ->get();

    echo "📋 Tìm thấy " . $records->count() . " bản ghi trong bảng {$table}\n";

    $updated = 0;
    $notFound = 0;

    $cc = 0;
    $total = $records->count();
    foreach ($records as $record) {
        $oldUid = $record->$uidField;

        $cc++;

        // Tìm user có old_id = $oldUid
        $user = DB::table('users')
            ->select('id')
            ->where('old_id', $oldUid)
            ->first();

        if ($user) {
            $newUid = $user->id;

            // Cập nhật $uidField với ID mới
            DB::table($table)
                ->where('id', $record->id)
                ->update([$uidField => $newUid]);

            echo "✅$cc/$total.  {$table}[{$record->id}]: {$uidField} {$oldUid} -> {$newUid}\n";
            $updated++;
        } else {
            echo "❌ Không tìm thấy user có old_id = {$oldUid} cho {$table}[{$record->id}]\n";
            $notFound++;
        }
    }

    echo "🎉 Hoàn thành {$table}: Cập nhật {$updated}, Không tìm thấy {$notFound}\n\n";

    return [
        'updated' => $updated,
        'not_found' => $notFound,
        'total' => $records->count()
    ];
}

//Tìm tất cả các tên bảng trong db
$tables = DB::select("SHOW TABLES");
$tables = array_map('current', $tables);

foreach ($tables as $table) {

    $char = $table[0];
    // if($char < 'p')
    // {
    //     echo "\n Ignore < p: $table";
    //     continue;
    // }

    // Chỉ xử lý các bảng có cột user_id
    if (DB::getSchemaBuilder()->hasColumn($table, 'user_id')) {
        changeFieldUidOfTable($table, 'user_id');
    }

    // Nếu cần, có thể thêm các bảng khác với cột khác
    // if (DB::getSchemaBuilder()->hasColumn($table, 'author_id')) {
    //     changeFieldUidOfTable($table, 'author_id');
    // }

    // if (DB::getSchemaBuilder()->hasColumn($table, 'created_by')) {
    //     changeFieldUidOfTable($table, 'created_by');
    // }
}
// Test function
// changeFieldUidOfTable('file_uploads', 'user_id');
// changeFieldUidOfTable('posts', 'author_id');
// changeFieldUidOfTable('comments', 'user_id');

?>
