<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


require "/var/www/html/public/index.php";

$gid = $_GET['gid'] ?? '';

$link = "https://test1.pm33.net/webhook/d3d86de7-6296-4801-9bde-2ebff389ad57?gid=$gid";

$dm = getDomainHostName();
if($dm == 'test2023.mytree.vn'){
//    die("xxxx123");
    $link = "http://103.163.216.12:5673/webhook/6180fdfa-0026-4de0-9db7-0f3c0deeebf1?gid=$gid";
}

if(!$gid)
    die("Not gid");

$obj = \App\Models\CrmMessageGroup::where('gid', $gid)->first();
if(!$obj){
    die("Not found gid: $gid");
}

$ch = curl_init($link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

//echo "<br/>\n LINK = $link<br/>\n";
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($response);
//echo "</pre>";
//die();

$js = json_decode($response, true) or die("JSON decode error: " . json_last_error_msg());
if(!$js){
    die("No data from API");
}

$js = (object)$js;

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($js);
//echo "</pre>";
$gid1 = $js->groupId;
$gname = $js->name;
if($gid != $gid1)
    die("GID not match: $gid != $gid1");

    try{


$obj->name = $gname;
$obj->full_info = $response; // Store the full response from the API
$obj->addLog("Get group info from API: $link");
$obj->update();
    }
    catch (Throwable $e) { // For PHP 7
        echo "<br/>\n Error1: ".$e->getMessage();
    }
    catch (Exception $exception){
        echo "<br/>\n Error2: ".$exception->getMessage();
    }
//die("Group ID: $gid1, Group Name: $gname");
//ob_clean();

die("Group ID: $gid1, Group Name: $gname");

//echo ($response);

