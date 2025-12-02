<?php

require_once '../../index.php';
use Aws\S3\S3Client;

//--------------------
//Cách 1, laravel, ngắn gọn, với cấu hình khai báo trong /config/filesystems.php:
$allFile = \Illuminate\Support\Facades\Storage::disk('s3')->allFiles();
echo '<pre>';
print_r($allFile);
echo '</pre>';
($allFile);

//--------------------
//Cách 2: Php Thuần
//Khởi tạo S3 Client
$s3 = new S3Client([
    'version' => 'latest',
    'use_path_style_endpoint' => true,
    'region' => 'local',
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
    'endpoint' => env('AWS_ENDPOINT'),
    //    'http' => [
    //        'verify' => false, // Tắt kiểm tra SSL certificate
    //    ],
]);

$bucketName = 'lad02'; // Thay thế bằng tên bucket của bạn

$ret = $s3->listObjectsV2(['Bucket' => $bucketName]);

// Liệt kê các đối tượng trong bucket
$objects = $s3->listObjects([
    'Bucket' => $bucketName,
]);

// In ra danh sách các đối tượng
$cc = 0;
foreach ($objects['Contents'] as $object) {
    $cc++;
    echo "\n #$cc: ";
    echo 'Tên đối tượng: '.$object['Key'].'<br>';
    echo 'Kích thước: '.$object['Size'].' bytes<br>';
    echo 'Thời gian tạo: '.$object['LastModified']->format('Y-m-d H:i:s').'<br><br>';
}
