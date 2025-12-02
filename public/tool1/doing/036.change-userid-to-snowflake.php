<?php

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';

require_once __DIR__.'/../../index.php';

// ========== SIMPLE USER ID TO SNOWFLAKE MIGRATOR ==========

function updateUsersToSnowflakeId() {
    echo "🔄 Bắt đầu cập nhật User ID thành SnowFlake...\n";

    // Tắt foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    // Lấy tất cả users
    $users = DB::table('users')->select('id', 'email')->get();

    echo "📋 Tìm thấy " . $users->count() . " users\n";

    $count = 0;
    foreach ($users as $user) {
        usleep(500);
        $oldId = $user->id;
        $newId = \GlxSnowflake::id();

        // Update user ID
        DB::table('users')->where('id', $oldId)->update(['id' => $newId]);

        echo "✅ Updated: {$oldId} -> {$newId} ({$user->email})\n";
        $count++;
    }

    // Bật lại foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    echo "🎉 Hoàn thành! Đã cập nhật {$count} users\n";
}

// Chạy function
updateUsersToSnowflakeId();

?>
