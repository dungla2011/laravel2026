<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['SERVER_NAME'] = 'galaxyz.net';

//$_SERVER['SERVER_NAME'] = 'japan1.galaxycloud.vn';
//$_SERVER['SERVER_NAME'] = '4share.vn';


$_SERVER['SERVER_NAME'] = 'japan1.galaxycloud.vn';
require_once "/var/www/galaxycloud/application/library/base/tool_glx.php";

echo '\n\nFix:\ncurl -XPUT -H "Content-Type: application/json" http://localhost:9200/_cluster/settings -d \'{ "transient": { "cluster.routing.allocation.disk.threshold_enabled": false } }\'
\ncurl -XPUT -H "Content-Type: application/json" http://localhost:9200/_all/_settings -d \'{"index.blocks.read_only_allow_delete": null}\'
\n\n';

ClassNetwork::forwardToOtherDomain($_SERVER['HTTP_HOST']);
require_once "/var/www/galaxycloud/index.php";;
$db = MysqliDb::getInstance();

require 'vendor/elastic/vendor/autoload.php';

use Elasticsearch\ClientBuilder;

check_unique_script();

function ol1($str)
{
    $file = "/var/glx/weblog/elastic_index.log";
    echo "<br/>\n $str";
    outputT($file, $str);
}

while (1) {

    try {

        ClassMongoDb::changeDbToGlx();
        \Base\clsMonitorAlert::createOrUpdateTimerAlert(__FILE__, _NSECOND_DAY);
        ClassMongoDb::changeDbToMultiSite();

        ol1(' --- Start all index ');
        indexDataNewsMongo(1);

        indexDataNewsMongo(2);

    } catch (Throwable $e) { // For PHP 7
        sleep(15);
        echo "<br/>\n Error1: " . $e->getMessage();
    } catch (Exception $exception) {
        sleep(15);
        echo "<br/>\n Error2: " . $exception->getMessage();
    }

    ol1(' ---  Stop all index, sleep 3600 ');

    sleep(3600);
}

function indexDataNewsMongo($newsOrData = 1)
{

    $host = [
        [
            'host' => "127.0.0.1",
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

    if ($newsOrData == 1)
        $obj = new modelNewsFile2();
    if ($newsOrData == 2)
        $obj = new modelDataFile2();

    echo "<br>DBx = " . $obj->getDbName();

    $dbName = $obj->getElasticDbName(1);

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
    }

    $mlang = \Base\clang::getArrLangEnable();

    //echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //print_r($mlang);
    //echo "</pre>";

    if (isCli()) {
        ol1(" -- Start index $dbName " . get_class($obj));
        //$mm = $obj->getArrayWhere(['status' => 1]);

        if ($sid = ctool::getArgvParamCli('siteid'))
            $mm = $obj->getArrayWhere(['status' => 1, 'siteid' => $sid]);
        else
            $mm = $obj->getArrayWhere(['status' => 1, 'siteid' => ['$nin' => [0, 65, 71, 72, 73, 74, 75, 80, 81, 82, 83, 84, 85, 86]]]);

        $cc = 0;

        //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //    print_r($mm);
        //    echo "</pre>";

        $mmIgnoreSid = [];
        $tt = count($mm);

        foreach ($mm as $obj) {

            usleep(1000);

            if ($obj instanceof modelDataFile2) ;

            $cc++;
            echo "<br/>\n $cc/$tt. ($dbName)  SID: $obj->siteid, $obj->name ";

            if (in_array($obj->siteid, $mmIgnoreSid)) {
                echo "<br/>\n Ignore SID ";
                continue;
            }

            //$obj->name = str_replace([',', []])
            $body = ['siteid' => $obj->siteid,
                'name' => $obj->name,
                'table' => $obj->getTableName(),
                'status' => $obj->status,
                'summary' => $obj->summary,
                'content' => $obj->content];

            $mfield = ['name_jp',
                'name_en',
                'content_jp',
                'content_en',
                'summary_jp',
                'summary_en'];

            foreach ($mfield as $field) {
                if (isset($obj->$field) && $obj->$field) {
                    $body[$field] = $obj->$field;
                }
            }

            if ($obj->createdAt) {
                $body['createdAt'] = strtotime($obj->createdAt);
            }
            if ($obj instanceof \Base\modelBaseMongo) {
                $body['mysql_or_mongo'] = "mg";
            }
            if ($obj instanceof \Base\ModelBase) {
                $body['mysql_or_mongo'] = "ms";
            }

            $prs = [
                'index' => $dbName,
                'id' => $obj->getId(),
                'type' => 'article_type',
                //'timestamp' => time(),
                'body' => $body
            ];

            $response = $client->index($prs);
        }
        ol1(" -- Stop index $dbName " . get_class($obj));
    }
}