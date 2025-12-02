<?php

/*
 * Update 02.2025
 * Review OK: 10.3.2015
 */

$_SERVER['SERVER_NAME'] = "4share.vn";

require_once "/var/www/html/public/index.php";;

if (!isCli()) {
    echo "ERROR: NOT CLI!";
    return;
}

//Tim danh sach user tai phim tu 2019, co log

$mm = \App\Models\TmpDownloadSession::select(["id", 'user_id', 'fid', 'name'])->get();

$cc = 0;
$tt = count($mm);
foreach ($mm as $one){

    $cc++;
    echo "\n$cc/$tt. $one->id ";

    if(!$one->name){
        if($name = \App\Models\FileUpload::withTrashed()->find($one->fid)?->name){
//            getch("... $name");
            $one->name = $name;
            $one->save();
            echo "\n Save name : $name";
        }
    }


}
