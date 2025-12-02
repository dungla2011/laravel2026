<?php

/*
 * Update 02.2025
 * Review OK: 10.3.2015
 */

$_SERVER['SERVER_NAME'] = "4share.vn";

require_once "/var/www/html/public/index.php";;

if (!isSupperAdmin_()) {
    echo "ERROR: Not allow!";
    return;
}

echo "/tool/4s/list_uploader_big.php?uid=...&delete=1";

$uid = request('uid');
$deleteCmd = request('delete');

if($uid){

    $nFile = \App\Models\FileUpload::where("user_id", $uid)->count();

    echo "<br/>\n NFILE = $nFile";

    if($deleteCmd)
    {
        \App\Models\FileUpload::where("user_id", $uid)->delete();

        echo "<br/>\n DELETE DONE!";
    }
    return;
}

echo \App\Components\U4sHelper::getUploaderSizeBig();

