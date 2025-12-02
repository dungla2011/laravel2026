<?php

require_once '../../index.php';
use Aws\S3\S3Client;

//--------------------
//Cách 2: Php Thuần
//Khởi tạo S3 Client
$s3 = new S3Client([
    'version' => 'latest',
    'use_path_style_endpoint' => true,
    'region' => 'local',
    'credentials' => [
        'key' => 'BGBWPk98RBugjlLFXGZG',
        'secret' => 'tBXVpKDM7E2WvnSPKweuTC6q9RgjR8P5ecjzTyes',
    ],
    'endpoint' => 'http://10.0.0.18:9000',
    //    'http' => [
    //        'verify' => false, // Tắt kiểm tra SSL certificate
    //    ],
]);

$bucketName = 'test1'; // Thay thế bằng tên bucket của bạn

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

    print_r($object);

    echo 'Tên đối tượng: '.$object['Key'].'<br>';
    echo 'Kích thước: '.$object['Size'].' bytes<br>';
    echo 'Thời gian tạo: '.$object['LastModified']->format('Y-m-d H:i:s').'<br><br>';
}
