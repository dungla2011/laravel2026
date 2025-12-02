<?php


use App\Models\FileUpload;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';


require_once "/var/www/html/public/index.php";

if(!isCli()){
    die(" NOT CLI!");
}
$mf = FileUpload::all();

foreach ($mf AS $item){
    echo "<br/>\n $item->id, $item->file_path";
    $fcl = \App\Models\FileCloud::where("id", $item->cloud_id)->first();
    if(file_exists($fcl->file_path)){
        echo "<br/>\n File OK";
        $tk = '1111118345353454142143982516974534523452345345345';
        $link = "https://tailieuchuan.net/api/member-file/upload";
        //$url, $tk, $fileCont, $fileName, $mime, $param = null
        $ret = FileUpload::uploadFileApiV2($link, $tk, file_get_contents($fcl->file_path), $item->name, $fcl->mime_type, ['refer' => "idx=".$item->id]);
//        getch('...: ' . $ret);
    }
    else{
        echo "<br/>\n File NOT OK";
    }
}
