<?php
error_reporting(E_ALL);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";

$mm = [
'5000'=>"Gói tải 1 ngày 5k",
'10000'=>"Gói tải 1 ngày 10k",
'15000'=>"Gói tải 3 ngày 15k",
'20000'=>"Gói tải 3 ngày 20k",
'25000'=>"Gói tải 3 ngày 25k",
'45000'=>"Gói tải 30 ngày 45k",
'50000'=>"Gói tải 30 ngày 50k",
'65000'=>"Gói tải 30 ngày 65k",
'100000'=>"Gói tải 100k",
'120000'=>"Gói tải 180 ngày 120k",
'150000'=>"Gói tải 180 ngày 150k",
'180000'=>"Gói tải 180 ngày 180k",
'200000'=>"Gói tải 180 ngày 200k",
'250000'=>"Gói tải 180 ngày 250k",
'300000'=>"Gói tải 180 ngày 300k",
'350000'=>"Gói tải 365 ngày 350k",
'480000'=>"Gói tải 365 ngày 480k",
'500000'=>"Gói tải 365 ngày 500k",
'590000'=>"Gói tải 365 năm 590k",
'1000000'=>"Gói tải 2 năm 1 triệu"];

foreach ($mm AS $price => $name){
    if(\App\Models\Product::where("price", $price)->where("parent_id", 4)->first())
        continue;
    $obj = new \App\Models\Product();
    $obj->price = $price;
    $obj->name = $name;
    $obj->parent_id = 4;
    $obj->save();
}
