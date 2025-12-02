<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DEF_TOOL_CMS', 1);

require_once '../index.php';

use MongoDB\Client;

// Kết nối đến MongoDB server
$client1 = new Client('mongodb://12.0.0.116:27017');
//$client1 = new Client("mongodb://10.0.0.19:27017");
$client2 = new Client('mongodb://10.0.0.19:27017');

// Chọn cơ sở dữ liệu và bộ sưu tập từ cả hai MongoDB
$db1 = $client1->test2019;
$db2 = $client2->test2019;

// Lấy danh sách tên của tất cả các bộ sưu tập từ cơ sở dữ liệu nguồn
$collections = $db1->listCollections();

foreach ($collections as $collectionInfo) {
    $collectionName = $collectionInfo->getName();
    $collection1 = $db1->$collectionName;
    $collection2 = $db2->$collectionName;

    // Lấy tất cả các tài liệu từ bộ sưu tập nguồn
    $documents = $collection1->find();

    if (! strstr($collectionName, '_meta_')) {
        continue;
    }

    echo "\n Sync $collectionName";

    // Lặp qua từng tài liệu và chèn hoặc cập nhật chúng vào cơ sở dữ liệu đích
    foreach ($documents as $document) {
        echo "\n $collectionName : ".$document['_id'];
        $collection2->replaceOne(['_id' => $document['_id']], $document, ['upsert' => true]);
    }

    echo "\n Bộ sưu tập $collectionName đã được đồng bộ hóa.\n";
}
