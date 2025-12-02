<?php

use App\Models\MyDocument;

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

//$_SERVER['SERVER_NAME'] = 'amazon2.galaxycloud.vn';
//$_SERVER['SERVER_NAME'] = 'japan1.galaxycloud.vn';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'tailieuchuan.net';

require_once "/var/www/html/public/index.php";
if(!isCli())
if(!isSupperAdmin_()){
    die("Not allow");
}
//    die("CLI only");


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
$dbName = MyDocument::getElasticDbName();

echo "<br/>\n DB: $dbName";
$prs = [
    'index' => $dbName
];

//Tạo db neu chua co
try{
    $response = $client->indices()->create($prs);
}
catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) { // For PHP 7
    echo "<br/>\n Error1: ".$e->getMessage();

}
catch (Exception $exception){
    echo "<br/>\n Error2: ".$exception->getMessage();
}

$response = $client->count($prs);

// Lấy số lượng bản ghi
$count = $response['count'];

echo("\n<br>Số lượng bản ghi: $count");
echo "<br/>\n";
if(!isCli())
    die("Not cli!");

function indexOrDelete($client, $dbName)
{
    $limit = 5;
    $from = 0;
    $cc = 0;

//$mm = \App\Models\FileUpload::limit($limit)->get();

    $mm = \App\Models\MyDocument::withTrashed()->latest()->get();
//    else
//        $mm = \App\Models\FileUpload::latest()->get();

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
        if($obj->created_at){
            $create = strtotime($obj->created_at);
        }
        else
            $create = 0;

        $body = [
            'id' => $obj->id,
            'name' => $name ." ". \LadLib\Common\cstring2::convert_codau_khong_dau($name),
            'summary'=> $obj->summary,
            'content'=> $obj->content,
            'created_at' => $create,
        ];



//                    $response = $client->update($prs);

        if($obj->deleted_at){
            $prs = [
                'index' => $dbName,
                'id' => $obj->id,
                'type' => 'article_type',
            ];
            echo "<br/>\n Delete...";
//            getch("....");

            try{
                $response = $client->delete($prs);
            }
            catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) { // For PHP 7
                echo "<br/>\n Error1: ".$e->getMessage();
                continue;
            }
            catch (Exception $exception){
                echo "<br/>\n Error2: ".$exception->getMessage();
                continue;
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

            //Kiem tra co chua, neu co roi thi update, chua thi index


            $response = $client->index($prs);
        }


        print_r($response ?? '');

    }



}


indexOrDelete($client, $dbName);
