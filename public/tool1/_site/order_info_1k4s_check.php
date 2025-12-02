<a href="/"> RETURNx </a>

<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '1k.4share.vn';

require_once "/var/www/html/public/index.php";

$link = \LadLib\Common\UrlHelper1::getUriWithoutParam();
if(isAdminLrv_()){
    echo "\n <a href='$link?get_all=1'> - </a> ";
    echo "\n <a href='$link'>.</a> ";
}

echo "<br/>\n";


$userIdFile = 3;

//Tìm tất cả các user_id  có tải của user 3
$distinctUserIds = \App\Models\DownloadLog::query()
    ->where("user_id_file", $userIdFile)
//        ->where('count_dl', '>', 0)
    ->distinct()
    ->pluck('user_id');
$mUidDownloadFile3 = $distinctUserIds->toArray();

//Tìm các file được tải bởi các user trên
$fileDlAll = \App\Models\DownloadLog::query()
    ->where('count_dl', '>', 0)
    ->whereIn("user_id", $mUidDownloadFile3)
//    ->where("user_id_file", "!=", $userIdFile)
    ->get()->toArray();

echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($fileDlAll);
echo "</pre>";


