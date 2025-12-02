<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";;

if(!isCli()){
    die(" NOT CLI!");
}
function getExt1($filepath, $widthDot = 0) {
    if($widthDot)
        return ".".strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    return strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
}


echo "\n-------------------";

$client = \Elastic\Elasticsearch\ClientBuilder::create()
    //->setHosts(["https://127.0.0.1:9200"])
    ->setHosts(["http://12.0.0.19:9200"])
//    ->setBasicAuthentication('admin','111111')
    ->setSSLVerification(false)->build();

echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($client->info());
echo "</pre>";



$dbName = "file_upload_4s2";
echo "<br/>\n DB: $dbName";
$prs = [
    'index' => $dbName
];

$limit = 5;
$from = 0;
$cc = 0;

//$mm = \App\Models\FileUpload::limit($limit)->get();
$mm = \App\Models\FileUpload::all();

$tt = count($mm);
$from += $limit;
foreach ($mm as $obj) {
//        if ($obj instanceof \Base\ModelCloudFile) ;
    $cc++;
    echo "<br/>\n $cc/$tt. $obj->id/ $obj->name";

//        continue;
    $name = strip_tags($obj->name);
//                    $sum = str_replace(['_', ',', '.', '-', '+', ':', "*", "/", '\\'], ' ', $obj->summary);
    $body = [
        'id' => $obj->id,
        'name' => $name,
        'size' => $obj->file_size,
        'user_id'=> $obj->user_id,
        'md5' => ($obj->md5),
        'ext' => getExt1($name),
        'count_download' => $obj->count_download,
        'crc32b' => ($obj->crc32),
        'created_at' => strtotime($obj->created_at),
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

    print_r($response);

}
