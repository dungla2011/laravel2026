<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['SERVER_NAME'] = 'galaxyz.net';
//$_SERVER['SERVER_NAME'] = 'amazon2.galaxycloud.vn';
//$_SERVER['SERVER_NAME'] = 'japan1.galaxycloud.vn';
$_SERVER['SERVER_NAME'] = '4share.vn';

require_once "/var/www/galaxycloud/application/library/base/tool_glx.php";
ClassNetwork::forwardToOtherDomain($_SERVER['SERVER_NAME']);
require_once "/var/www/galaxycloud/index.php";;
$db = MysqliDb::getInstance();

require 'vendor/elastic/vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$host = [
    [
        'host' => "glx.com.vn",
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
$dbName = $obj->getElasticDbName()."_4s";

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
    echo "<br/>\n Not exit ? $dbName ";
} else {
    echo "Index {$prs['index']} đã có rồi!";

// Get settings for one index

    $response = $client->indices()->getSettings($prs);
    $indices = $client->cat()->indices($prs);

    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    print_r($indices);
    echo "</pre>";

}
