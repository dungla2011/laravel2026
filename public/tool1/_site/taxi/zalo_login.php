<?php

try {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once "/var/www/html/public/index.php";

//  taxi_zalos
//  id	zalo_id	created_at	username	avatar

    $zalo_id = request('id', '');
// $username = request('userna me', '');
// $avatar = request('avatar', '');

    if (!$zalo_id) {
        die("Not zalo ID");
//        $zalo_id = 1;
    }

// Sử dụng larvel để insert vào db:, bảng taxi_zalos, field: id	zalo_id	created_at	username	avatar
// Dùng DB Raw:

    \Illuminate\Support\Facades\DB::table('taxi_zalos')->insert([
        'zalo_id' => $zalo_id,
        'name'=> request('name', ''),
        'created_at' => now(),
    ]);

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}


