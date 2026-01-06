<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../../../vendor/autoload.php';
$app = require_once __DIR__.'/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Lấy tất cả parameters từ request
$params = $request->all();

// Gọi hàm xử lý thanh toán BaoKim (class đã được define trong common.php)
clsBaoKim::buyVip($params);


