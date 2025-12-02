<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

//$_SERVER['SERVER_NAME'] = 'amazon2.galaxycloud.vn';
//$_SERVER['SERVER_NAME'] = 'japan1.galaxycloud.vn';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';

require_once "/var/www/html/public/index.php";

//OK:
$client = \Elastic\Elasticsearch\ClientBuilder::create()
    ->setHosts(["https://127.0.0.1:9200"])
    ->setBasicAuthentication('admin','111111')
    ->setSSLVerification(false)->build();

$dbName = "test_abc";
echo "<br/>\n DB: $dbName";
$prs = [
    'index' => $dbName
];

$limit = 5;
$from = 0;
$cc = 0;

$mm = \App\Models\FileUpload::limit($limit)->get();
$from += $limit;
foreach ($mm as $obj) {
//        if ($obj instanceof \Base\ModelCloudFile) ;
    $cc++;
    echo "<br/>\n $cc. $obj->id/ $obj->name";

//        continue;

   $name = ($obj->name);
//                    $sum = str_replace(['_', ',', '.', '-', '+', ':', "*", "/", '\\'], ' ', $obj->summary);
    $body = [
        'id' => $obj->id,
        'name' => $name,
//            'size' => $obj->filesize,
//            'userid'=>$obj->userid,
        'md5' => strtotime($obj->createdAt),
        'crc32b' => strtotime($obj->createdAt),
    ];

    $prs = [
        'index' => $dbName,
        'id' => $obj->id,
        'type' => 'article_type',
        //'timestamp' => time(),
        'body' => $body
    ];
//                    $response = $client->update($prs);
    $response = $client->index($prs);
}
