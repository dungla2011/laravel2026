<?php

use App\Models\FileUpload;

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

//$_SERVER['SERVER_NAME'] = 'amazon2.galaxycloud.vn';
//$_SERVER['SERVER_NAME'] = 'japan1.galaxycloud.vn';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';

require_once "/var/www/html/public/index.php";

if(!isSupperAdmin_()){
//    die("Not allow");
}
if(!isCli()){
    die("CLI only");
}

function getExt1($filepath, $widthDot = 0) {
    if($widthDot)
        return ".".strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    return strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
}

$svEl = 'elasticSv';
//OK:
$client = \Elastic\Elasticsearch\ClientBuilder::create()
//    ->setHosts(["https://127.0.0.1:9200"])
    ->setBasicAuthentication('elastic',env('ELASTIC_PASSWORD'))
    ->setHosts(["http://$svEl:9200"])
//    ->setHeader('Content-Type', 'application/json')
    ->setSSLVerification(false)->build();


//$dbName = "file_upload_4s";
$dbName = (new FileUpload())->getElasticDbName();

echo "<br/>\n DB: $dbName";
$prs = [
    'index' => $dbName
];

$response = $client->count($prs);

// Lấy số lượng bản ghi
$count = $response['count'];

echo("\n<br>Số lượng bản ghi: $count");
echo "<br/>\n";
if(!isCli())
    die("Not cli!");

function indexOrDelete($client, $dbName, $delete = 0)
{
    $limit = 5;
    $from = 0;
    $cc = 0;

//$mm = \App\Models\FileUpload::limit($limit)->get();

    if($dbName)
        $mm = \App\Models\FileUpload::withTrashed()->latest()->get();
    else
        $mm = \App\Models\FileUpload::latest()->get();

    $tt = count($mm);
    $from += $limit;
    foreach ($mm as $obj) {
//        if ($obj instanceof \Base\ModelCloudFile) ;
        $cc++;
        echo "<br/>\n $cc/$tt. $obj->id/ $obj->created_at, delete = $obj->deleted_at, $obj->name";

//        continue;
        $name = strip_tags($obj->name);
        $name = fixFileNameToIndexElastic($name);
//    $sum = fixFileNameToIndexElastic($obj->summary);
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



//                    $response = $client->update($prs);

        if($obj->deleted_at){
            $prs = [
                'index' => $dbName,
                'id' => $obj->id,
                'type' => 'article_type',
//            'body' => $body,
//            'client' => [
//            'headers' => [
//                    'Content-Type' => 'application/json',
//                ],
//            ],
            ];
            echo "<br/>\n Delete...";
//            getch("....");

            try{
                $response = $client->delete($prs);
            }
            catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) { // For PHP 7
                echo "<br/>\n Error1: ".$e->getMessage();
            }
            catch (Exception $exception){
                echo "<br/>\n Error2: ".$exception->getMessage();
            }
        }else {
            $prs = [
                'index' => $dbName,
                'id' => $obj->id,
                'type' => 'article_type',
                'body' => $body,
//            'client' => [
//            'headers' => [
//                    'Content-Type' => 'application/json',
//                ],
//            ],
            ];
            echo "<br/>\n Index...";
            $response = $client->index($prs);
        }


        print_r($response ?? '');

    }



}

//Kiem tra neu co delete o argv
$delete = 0;
if($argv[1] ?? ''){
    $delete = 1;
}

getch("DELETE = $delete");

indexOrDelete($client, $dbName, $delete);



$response = $client->count($prs);

// Lấy số lượng bản ghi
$count1 = $response['count'];

echo "\n\n NewCount = $count1 / Old = $count";
