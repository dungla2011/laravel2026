<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "4share.vn";
require_once "/var/www/html/public/index.php";
check_unique_script();

///var/www/galaxycloud/tool/_site/galaxyz/elastic_sync_4s.php
$ret = shell_exec("ps -ef |grep elastic_sync_4s ");
echo "<br/>\n $ret";
if(!strstr($ret, "var/www/galaxycloud/tool/_site/galaxyz/elastic_sync_4s.php")){
    echo "<br/>\n Error:NotFoundProcessRunning?";
}

use Elasticsearch\ClientBuilder;

//Tìm 1 file gần nhất, cách 6 tiếng
// Tìm tên file đó nếu thấy trong index là ok

$timeCheck =  nowyh(time() - 3600 * 6);

//Các file < timeCheck, sẽ phải có trong Elastic
$mm = \Base\ModelCloudFile::getArrayWhereSql(" createdAt < '$timeCheck' ORDER BY id DESC LIMIT 100");

$idFind = $nameSearch = '';
foreach ($mm AS $obj){
//    echo "\n $obj->id, $obj->createdAt ,  $obj->name";
    if($obj->createdAt < $timeCheck){
        $nameSearch = $obj->name;

        $idFind = $obj->getId();
        break;
    }
}

if(!$idFind)
    die("Error: not found id?");

echo "<br/>\n $nameSearch , $obj->createdAt";

//return;

$host = [
    [
//        'host' => "glx.com.vn",
        'host' => "localhost",
        'port' => "9200",
        'scheme' => "http",
    ]
];

$client = ClientBuilder::create()->setHosts($host)->build();
//$ind = $client->cat()->indices();
//
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($ind);
//echo "</pre>";
////echo "<br/>\nxxx";
///

$obj = new \Base\ModelCloudFile();
$dbName = $obj->getElasticDbName() . "_4s";

echo "<br>DBx: " . $obj->getDbName();

echo "<br/>\n DB: $dbName";
echo "<br/>\n";
$prs0 = [
    'index' => $dbName
];

$prs = [
    'index' => $dbName
];

//Kiểm tra xem Index đã tồn tại không
$indexExist = $client->indices()->exists($prs);

if (!$indexExist) {
    try {
        //Thực hiện tạo Index
        $response = $client->indices()->create($prs);

        echo "<br/>\n Tạo ok?";

    } catch (Exception $e) {
        //Lỗi tạo Index
        $res = json_decode($e->getMessage());
        echo $res->error->reason;
    }
} else {
    echo "Index {$prs['index']} đã có rồi!";

    $indices = $client->cat()->indices($prs);

//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($indices);
//    echo "</pre>";

}

$mlang = \Base\clang::getArrLangEnable();
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mlang);
//echo "</pre>";
//

$nameSearch = str_replace(['_', ',', '.', '-', '+', ':', "*", "/", '\\'], ' ', $nameSearch);
$nameSearch = clsElasticHelper::fixNameFileToIndex($nameSearch);

//$nameSearch = "Dishonored GOTY LinkNeverDie Com rar";

echo "<br/>\n TEST SEARCH: ";
//Test search
$prs = [
    'index' => "$dbName",
    'type' => 'article_type',
    'size' => 10,
    'body' => [
        'query' => [
            'query_string' => [
                'query' => "\"$nameSearch\""
            ]
        ]
    ]
];

$response = $client->search($prs);

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($response);
//echo "</pre>";

if (!$response) {
    echo "<br/>\n Not found result?";
}
{

    if (isset($response['hits']['hits'])) {
        $total = $response['hits']['total']['value'];
        echo "<br/>\n Total = $total ";
    }

//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($response['hits']);
//    echo "</pre>";

    foreach ($response['hits']['hits'] as $hit) {

        unset($hit['_source']['content']);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($hit);
//        echo "</pre>";

        $name = $hit['_source']['name'];
        $sum = $hit['_source']['summary'];
        if (isset($hit['_source']['content']))
            $cont = $hit['_source']['content'];
        $id = $hit['_id'];

        if($id == $idFind)
            echo "<br/>\n ElasticSearchFoundSomeRowInMysql ($id == $idFind) ";

        echo "<br/>\n FID = $id . $name  ";

    }
}
