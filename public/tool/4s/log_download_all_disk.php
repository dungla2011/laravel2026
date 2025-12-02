<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);

$_SERVER['SERVER_NAME'] = '4share.vn';
require_once "/var/www/html/public/index.php";

if(!isSupperAdmin_()){
   die("Not admin!");
}

$mDate = [];

for($i = 0; $i < 20; $i++){
    $mDate[] = date("Y-m-d", time() -  _NSECOND_DAY * $i);

}
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mDate);
//echo "</pre>";

$m2 = \App\Models\CloudServer::all();
$cc =0;

$mServerLocation = [];
foreach ($m2 AS $sv1){

    if($sv1->enable){

//        echo "\n <br> $sv1->domain | $sv1->ip_internet | $sv1->mount_list";
        $mdisk = explode(",", $sv1->mount_list);

        foreach ($mdisk AS $disk){
            $mServerLocation["$sv1->domain.$disk"] = [];

            foreach ($mDate AS $date){
                $mServerLocation["$sv1->domain.$disk"][$date] = 0;
            }

            $cc++;
//            echo "<tr>";
//            echo "<td>";
//            echo "\n $cc. $sv1->domain | $disk";
//            echo "</td>";
//            echo "</tr>";
        }

    }

}
//
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mServerLocation);
//echo "</pre>";
//
//return;

//$mm = modelLogDownload4S::getArrayWhere_([], ['sort'=>['time'=>-1], 'limit'=>5000]);
$mm = \App\Models\TmpDownloadSession::latest()->limit(5000)->get();

$cc = 0;
foreach ($mm AS $obj){
    $cc++;
//    echo "<br/>\n";
//    echo "\n $cc . $obj->time | $obj->fid";
//    $file = \Base\ModelCloudFile::getOne_($obj->fid);

    $datex = substr($obj->created_at, 0,10);

    if(isset($mServerLocation[$obj->server . ".". $obj->location]))
    if(isset($mServerLocation[$obj->server . ".". $obj->location][$datex]))
        $mServerLocation[$obj->server . ".". $obj->location][$datex]++;

//    if(!isset($mDate[$datex]))
//        $mDate[$datex] = [];
//    if(!isset($mDate[$datex][$file->server1 . ".". $file->location1]))
//        $mDate[$datex][$file->server1 . ".". $file->location1] = 0;
//    $mDate[$datex][$file->server1 . ".". $file->location1] ++;


}

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mServerLocation);
//echo "</pre>";

echo "\n<table border='1'>";
$cc = 0;
echo "\n<tr>";
echo "\n<td></td><td></td>";
echo "\n";
foreach ($mDate AS $date1){

    echo "\n<td> $date1 ";
    echo "\n</td>";
}
echo "\n<td>TT ";
echo "\n</td>";
echo "\n</tr>";
$tt = 0;
$countRow = 0;
foreach ($mServerLocation AS $svName => $dateCount){

    $cc++;
    echo "\n<tr>";
    echo "\n<td> $cc </td>";
    echo "\n<td> $svName  </td>";

    foreach ($dateCount AS $date=>$count){
        if(!$count)
            echo "\n<td style='color: red; border: 1px solid red'> $count </td> ";
        else
            echo "\n<td> $count </td> ";
        $tt+=$count;
        $countRow += $count;
    }
    echo "\n<td> $countRow </td> ";
    echo "\n</tr>";

}
echo "\n</table>";


echo "<br/>\n TT = $tt";
